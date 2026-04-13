<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ১. মাস্টার টেবিল (রিটার্নের মূল তথ্য)
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_no')->unique(); // PR-0001
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade'); // কোন বিলের এগেইন্সটে
            $table->date('return_date');
            $table->decimal('total_return_amount', 15, 2); // সব আইটেম মিলিয়ে মোট কত টাকার মাল ফেরত গেল
            $table->string('reason')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // ২. ডিটেইলস টেবিল (কোন আইটেম কত পিস রিটার্ন হলো)
        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_return_id')->constrained('purchase_returns')->onDelete('cascade');

            // আপনার যদি products নামে টেবিল থাকে, তবে সেটার আইডি
            // $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');

            $table->string('product_name'); // আইটেমের নাম
            $table->decimal('return_qty', 10, 2); // কত পিস ফেরত দিচ্ছে
            $table->decimal('unit_price', 15, 2); // কেনার রেট কত ছিল
            $table->decimal('total_price', 15, 2); // return_qty * unit_price
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_return_items');
        Schema::dropIfExists('purchase_returns');
    }
};
