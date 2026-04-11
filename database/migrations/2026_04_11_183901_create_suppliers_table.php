<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Auto generated ID like SUP-0001');
            $table->string('company_name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('material_type')->nullable()->comment('Raw Material, Chemicals, Packaging etc.');

            // Financial Balances
            $table->decimal('opening_balance', 15, 2)->default(0)
                  ->comment('Positive means we owe them (Payable), Negative means Advance given');
            $table->decimal('current_balance', 15, 2)->default(0)
                  ->comment('Real-time tracking of current due');

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes(); // Prevent accidental permanent deletion
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
