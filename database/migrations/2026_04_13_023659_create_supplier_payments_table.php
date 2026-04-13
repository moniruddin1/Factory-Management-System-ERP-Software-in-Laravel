<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no')->unique(); // পেমেন্ট ভাউচার নম্বর (যেমন: PAY-0001)
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade'); // কোন সাপ্লায়ারকে পেমেন্ট করা হলো

            // purchase_id 'nullable' রাখা হয়েছে কারণ পেমেন্ট কোনো নির্দিষ্ট বিলের বিপরীতে হতে পারে, আবার অগ্রিমও হতে পারে।
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->onDelete('set null');

            $table->decimal('amount', 12, 2); // কত টাকা দেওয়া হলো
            $table->date('payment_date'); // পেমেন্টের তারিখ
            $table->string('payment_method'); // Cash, Bank, Mobile Banking (bKash/Nagad) ইত্যাদি
            $table->string('transaction_ref')->nullable(); // চেক নম্বর বা ট্রানজেকশন আইডি
            $table->text('note')->nullable(); // কোনো নোট বা রিমার্কস

            $table->foreignId('created_by')->constrained('users'); // কে পেমেন্ট এন্ট্রি করেছে
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_payments');
    }
};
