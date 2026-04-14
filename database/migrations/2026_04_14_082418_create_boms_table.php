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
            Schema::create('boms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('finished_product_id')->constrained('products')->onDelete('cascade')->comment('The shoe being manufactured');
                $table->string('name')->comment('e.g., Formula for Oxford Black Shoe');
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boms');
    }
};
