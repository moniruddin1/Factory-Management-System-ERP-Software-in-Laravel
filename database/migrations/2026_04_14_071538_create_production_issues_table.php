<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('production_issues', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no')->unique(); // ইনভয়েস নম্বর (e.g. ISSUE-001)
            $table->date('date');
            $table->string('issued_to'); // স্টাফ বা কারিগর যাকে দেওয়া হচ্ছে
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_issues');
    }
};
