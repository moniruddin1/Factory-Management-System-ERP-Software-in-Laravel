<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierReportController extends Controller
{
    // রিপোর্ট ড্যাশবোর্ড
    public function index()
    {
        return view('supplier-reports.index');
    }

    // All Supplier Due/Balance Report
    public function dueReport(Request $request)
    {
        // যাদের ব্যালেন্স ০ নয়, শুধু তাদের ডাটা আনছি
        $suppliers = Supplier::where('current_balance', '!=', 0)
                        ->orderBy('current_balance', 'desc') // যাদের কাছে বেশি টাকা পাবো তারা আগে থাকবে
                        ->get();

        // টোটাল সামারি হিসাব করা
        $totalPayable = $suppliers->where('current_balance', '>', 0)->sum('current_balance');
        $totalAdvance = $suppliers->where('current_balance', '<', 0)->sum('current_balance');

        return view('supplier-reports.due-report', compact('suppliers', 'totalPayable', 'totalAdvance'));
    }
public function purchaseReport(Request $request)
{
    // ডিফল্ট ফিল্টার: চলতি মাসের ১ তারিখ থেকে আজ পর্যন্ত
    $startDate = $request->input('start_date', date('Y-m-01'));
    $endDate = $request->input('end_date', date('Y-m-d'));

    // আপনার আসল মডেল 'Purchase' ব্যবহার করা হয়েছে
    $purchases = \App\Models\Purchase::with('supplier')
                    ->whereBetween('purchase_date', [$startDate, $endDate])
                    ->orderBy('purchase_date', 'asc')
                    ->get();

    // আপনার মডেলের 'grand_total' কলামটি ব্যবহার করে মোট হিসাব করা হচ্ছে
    $totalPurchase = $purchases->sum('grand_total');

    return view('supplier-reports.purchase-report', compact('purchases', 'startDate', 'endDate', 'totalPurchase'));
}
// Date-wise Payment Summary Report
    public function paymentReport(Request $request)
    {
        // ডিফল্ট ফিল্টার: চলতি মাসের ১ তারিখ থেকে আজ পর্যন্ত
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        // আপনার Payment মডেল কল করে ডাটা আনুন
        // মডেলের নাম এবং কলামের নাম আপনার ডাটাবেস অনুযায়ী মিলিয়ে নিন
        $payments = \App\Models\SupplierPayment::with('supplier')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date', 'asc')
            ->get();

        $totalPayment = $payments->sum('amount'); // total amount হিসাব করা

        return view('supplier-reports.payment-report', compact('payments', 'startDate', 'endDate', 'totalPayment'));
    }
}
