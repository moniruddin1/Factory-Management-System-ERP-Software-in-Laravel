<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->string('batch_no')->nullable(); // ব্যাচ ম্যানেজমেন্টের জন্য
            $table->decimal('quantity', 10, 2)->default(0.00); // বর্তমান স্টক
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_stocks');
    }
};
