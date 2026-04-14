<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('productions', function (Blueprint $table) {
            // null হতে পারে, কারণ মাঝে মাঝে ডাইরেক্ট প্রোডাকশনও হতে পারে ভাউচার ছাড়া
            $table->foreignId('production_issue_id')->nullable()->after('reference_no')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productions', function (Blueprint $table) {
            //
        });
    }
};
