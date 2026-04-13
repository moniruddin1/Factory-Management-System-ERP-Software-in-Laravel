<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Purchases টেবিলে নতুন ফিল্ড যোগ করা
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('invoice_type')->default('Credit')->after('purchase_date'); // Cash, Credit, Advance
            $table->string('reference_no')->nullable()->after('invoice_type'); // Challan or PO No

            // হিসাব-নিকাশের নতুন ফিল্ড (grand_total এর পরে)
            $table->decimal('tax_amount', 12, 2)->default(0)->after('grand_total');
            $table->decimal('shipping_cost', 12, 2)->default(0)->after('tax_amount');
            $table->decimal('other_charges', 12, 2)->default(0)->after('shipping_cost');
            $table->decimal('round_adjustment', 12, 2)->default(0)->after('other_charges');

            $table->decimal('paid_amount', 12, 2)->default(0)->after('round_adjustment');
            $table->decimal('due_amount', 12, 2)->default(0)->after('paid_amount');
            $table->string('payment_method')->nullable()->after('due_amount'); // Cash, Bank, Mobile Banking
        });

        // Purchase Items টেবিলে নতুন ফিল্ড যোগ করা
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->decimal('discount', 10, 2)->default(0)->after('unit_price'); // Item-wise discount
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_type', 'reference_no', 'tax_amount', 'shipping_cost',
                'other_charges', 'round_adjustment', 'paid_amount', 'due_amount', 'payment_method'
            ]);
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
};
