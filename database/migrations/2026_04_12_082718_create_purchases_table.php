<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->date('purchase_date');

            // হিসাব-নিকাশ
            $table->decimal('total_amount', 12, 2)->default(0); // মোট দাম
            $table->decimal('discount', 12, 2)->default(0);     // ডিসকাউন্ট
            $table->decimal('grand_total', 12, 2)->default(0);  // চূড়ান্ত দাম

            // স্ট্যাটাস ও নোট
            $table->string('status')->default('Pending'); // Pending, Received, Cancelled
            $table->text('note')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
