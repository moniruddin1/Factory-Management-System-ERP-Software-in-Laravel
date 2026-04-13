<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\SupplierPayment;
use App\Models\PurchaseReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierLedgerController extends Controller
{
    public function index(Request $request)
    {
        // সব সাপ্লায়ারের লিস্ট ড্রপডাউনের জন্য (বিনা ফিল্টারে)
        $allSuppliers = Supplier::orderBy('company_name', 'asc')->get();

        // ফিল্টার লজিক
        $query = Supplier::query();

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('company_name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        if ($request->supplier_id) {
            $query->where('id', $request->supplier_id);
        }

        if ($request->material_type) {
            $query->where('material_type', $request->material_type);
        }

        $limit = $request->limit ?? 25;
        $suppliers = $query->paginate($limit)->withQueryString();

        return view('supplier-ledgers.index', compact('suppliers', 'allSuppliers'));
    }
public function showStatement(Request $request, $id)
{
    $supplier = \App\Models\Supplier::findOrFail($id);

    // ডিফল্ট ফিল্টার: চলতি মাসের ১ তারিখ থেকে আজ পর্যন্ত
    $startDate = $request->input('start_date', date('Y-m-01'));
    $endDate = $request->input('end_date', date('Y-m-d'));

    // ১. Start Date এর আগের ওপেনিং ব্যালেন্স হিসাব করা
    // ধরে নিচ্ছি Credit = আমরা পাবো (Payable), Debit = আমরা দিয়েছি (Payment/Advance)
    $previousCredit = \App\Models\SupplierTransaction::where('supplier_id', $id)
                        ->whereDate('transaction_date', '<', $startDate)
                        ->sum('credit');

    $previousDebit = \App\Models\SupplierTransaction::where('supplier_id', $id)
                        ->whereDate('transaction_date', '<', $startDate)
                        ->sum('debit');

    $openingBalance = ($supplier->initial_opening_balance ?? 0) + $previousCredit - $previousDebit;

    // ২. সিলেক্ট করা তারিখের লেনদেনগুলো আনা
    $transactions = \App\Models\SupplierTransaction::where('supplier_id', $id)
                        ->whereBetween('transaction_date', [$startDate, $endDate])
                        ->orderBy('transaction_date', 'asc')
                        ->get();

    return view('supplier-ledgers.statement', compact('supplier', 'startDate', 'endDate', 'openingBalance', 'transactions'));
}

    // ২. Show Method: নির্দিষ্ট সাপ্লায়ারের বিস্তারিত লেজার (A-Z History)
    public function show(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Purchase Data (Credit - আমরা মাল কিনেছি, তাই পাওনা বেড়েছে)
        $purchases = Purchase::where('supplier_id', $id)
            ->when($startDate && $endDate, function($q) use ($startDate, $endDate) {
                return $q->whereBetween('purchase_date', [$startDate, $endDate]);
            })
            ->select('id', 'purchase_date as date', 'invoice_no as ref_no', 'grand_total as credit', DB::raw('0 as debit'), DB::raw("'Purchase' as type"))
            ->get();

        // Payment Data (Debit - আমরা টাকা দিয়েছি, তাই পাওনা কমেছে)
        $payments = SupplierPayment::where('supplier_id', $id)
            ->when($startDate && $endDate, function($q) use ($startDate, $endDate) {
                return $q->whereBetween('payment_date', [$startDate, $endDate]);
            })
            ->select('id', 'payment_date as date', 'voucher_no as ref_no', DB::raw('0 as credit'), 'amount as debit', DB::raw("'Payment' as type"))
            ->get();

        // Return Data (Debit - আমরা মাল ফেরত দিয়েছি, তাই পাওনা কমেছে)
        $returns = PurchaseReturn::where('supplier_id', $id)
            ->when($startDate && $endDate, function($q) use ($startDate, $endDate) {
                return $q->whereBetween('return_date', [$startDate, $endDate]);
            })
            ->select('id', 'return_date as date', 'return_no as ref_no', DB::raw('0 as credit'), 'total_return_amount as debit', DB::raw("'Purchase Return' as type"))
            ->get();

        // সব ডাটা একসাথে করে তারিখ অনুযায়ী সাজানো (Oldest to Newest)
        $transactions = $purchases->concat($payments)->concat($returns)->sortBy('date');

        return view('supplier-ledgers.show', compact('supplier', 'transactions', 'startDate', 'endDate'));
    }
}
