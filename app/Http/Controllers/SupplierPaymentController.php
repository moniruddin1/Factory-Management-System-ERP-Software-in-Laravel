<?php

namespace App\Http\Controllers;

use App\Models\SupplierPayment;
use App\Models\Supplier;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierPaymentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->per_page ?? 25;
        $search = $request->search;

        // --- ১. Payment History Query ---
        $paymentQuery = \App\Models\SupplierPayment::with(['supplier', 'purchase']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $paymentQuery->whereBetween('payment_date', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('supplier_id')) {
            $paymentQuery->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('search')) {
            $paymentQuery->where(function($q) use ($search) {
                $q->where('voucher_no', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($q2) use ($search) {
                      $q2->where('company_name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('id', $search);
                  })
                  ->orWhereHas('purchase', function($q3) use ($search) {
                      $q3->where('invoice_no', 'like', "%{$search}%");
                  });
            });
        }
        // 'payment_page' নামে আলাদা পেজিনেশন
        $payments = $paymentQuery->latest()->paginate($perPage, ['*'], 'payment_page')->appends($request->all());

        // --- ২. Pending Dues Query ---
        $dueQuery = \App\Models\Purchase::with('supplier')->where('due_amount', '>', 0);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $dueQuery->whereBetween('purchase_date', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('supplier_id')) {
            $dueQuery->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('search')) {
            $dueQuery->where(function($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($q2) use ($search) {
                      $q2->where('company_name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('id', $search);
                  });
            });
        }
        // 'due_page' নামে আলাদা পেজিনেশন
        $dueInvoices = $dueQuery->latest()->paginate($perPage, ['*'], 'due_page')->appends($request->all());

        // --- ৩. অন্যান্য ডাটা ---
        $suppliers = \App\Models\Supplier::select('id', 'company_name', 'phone')->get();
        $totalPaid = \App\Models\SupplierPayment::sum('amount');
        $totalDue = \App\Models\Purchase::sum('due_amount');

        return view('supplier-payments.index', compact('payments', 'suppliers', 'dueInvoices', 'totalPaid', 'totalDue'));
    }

    // নতুন পেমেন্ট ফর্ম দেখানোর জন্য
        public function create(Request $request)
        {
            // এখানে 'phone' যুক্ত করা হয়েছে
            $suppliers = Supplier::select('id', 'company_name', 'phone')->where('is_active', 1)->get();

            $selectedPurchase = null;
            if ($request->has('purchase_id')) {
                $selectedPurchase = Purchase::with('supplier')->find($request->purchase_id);
            }

            return view('supplier-payments.create', compact('suppliers', 'selectedPurchase'));
        }

        // পেমেন্ট সেভ করার লজিক
            public function store(Request $request)
            {
                $request->validate([
                    'supplier_id' => 'required|exists:suppliers,id',
                    'amount' => 'required|numeric|min:1',
                    'payment_date' => 'required|date',
                    'payment_method' => 'required|string',
                    'purchase_id' => 'nullable|exists:purchases,id'
                ]);

                try {
                    DB::beginTransaction();

                    $amountToPay = $request->amount;
                    $supplierId = $request->supplier_id;
                    $purchaseId = $request->purchase_id;

                    // --- লজিক চেক: Overpayment Alert ---
                    if ($purchaseId) {
                        $purchase = Purchase::where('id', $purchaseId)->where('supplier_id', $supplierId)->first();

                        if (!$purchase) {
                            return back()->with('error', 'Invalid Invoice ID for the selected supplier.')->withInput();
                        }

                        if ($amountToPay > $purchase->due_amount) {
                            return back()->with('error', 'Overpayment Alert: Payment amount (৳'.$amountToPay.') is greater than the invoice due amount (৳'.$purchase->due_amount.').')->withInput();
                        }
                    } else {
                        $totalDue = Purchase::where('supplier_id', $supplierId)->sum('due_amount');

                        if ($amountToPay > $totalDue) {
                            return back()->with('error', 'Overpayment Alert: Payment amount (৳'.$amountToPay.') is greater than the total pending dues (৳'.$totalDue.') for this supplier.')->withInput();
                        }
                    }

                    // --- ভাউচার জেনারেট ---
                    $lastPayment = SupplierPayment::orderBy('id', 'desc')->first();
                    $nextId = $lastPayment ? $lastPayment->id + 1 : 1;
                    $voucherNo = 'PAY-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

                    // --- পেমেন্ট এন্ট্রি সেভ ---
                    SupplierPayment::create([
                        'voucher_no' => $voucherNo,
                        'supplier_id' => $supplierId,
                        'purchase_id' => $purchaseId,
                        'amount' => $amountToPay,
                        'payment_date' => $request->payment_date,
                        'payment_method' => $request->payment_method,
                        'transaction_ref' => $request->transaction_ref,
                        'note' => $request->note,
                        'created_by' => Auth::id(),
                    ]);

                    // --- ইনভয়েস আপডেট লজিক ---
                    if ($purchaseId) {
                        // Case A: নির্দিষ্ট ইনভয়েসের জন্য
                        $purchase->paid_amount += $amountToPay;
                        $purchase->due_amount -= $amountToPay;
                        $purchase->due_amount = round($purchase->due_amount, 2);

                        // এখানে payment_status এর জায়গায় status দেওয়া হয়েছে
                        $purchase->status = ($purchase->due_amount <= 0) ? 'Paid' : 'Partial';
                        $purchase->save();

                    } else {
                        // Case B: অটোমেটিক অ্যাডজাস্টমেন্ট (FIFO Method)
                        $pendingPurchases = Purchase::where('supplier_id', $supplierId)
                            ->where('due_amount', '>', 0)
                            ->orderBy('id', 'asc')
                            ->get();

                        $remainingAmount = $amountToPay;

                        foreach ($pendingPurchases as $pendingInvoice) {
                            if ($remainingAmount <= 0) {
                                break;
                            }

                            $payForThisInvoice = min($remainingAmount, $pendingInvoice->due_amount);

                            $pendingInvoice->paid_amount += $payForThisInvoice;
                            $pendingInvoice->due_amount -= $payForThisInvoice;
                            $pendingInvoice->due_amount = round($pendingInvoice->due_amount, 2);

                            // এখানেও payment_status এর জায়গায় status দেওয়া হয়েছে
                            $pendingInvoice->status = ($pendingInvoice->due_amount <= 0) ? 'Paid' : 'Partial';
                            $pendingInvoice->save();

                            $remainingAmount -= $payForThisInvoice;
                        }
                    }

                    DB::commit();
                    return redirect()->route('supplier-payments.index')->with('success', 'Payment recorded successfully.');

                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->with('error', 'Something went wrong: ' . $e->getMessage())->withInput();
                }
            }
}
