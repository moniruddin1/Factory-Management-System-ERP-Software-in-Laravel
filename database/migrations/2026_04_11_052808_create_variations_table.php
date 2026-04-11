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
        Schema::create('variations', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Size', 'Color']); // To identify if it's a size or a color
            $table->string('name'); // Example: '40', '41' (for size) or 'Black', 'Brown' (for color)
            $table->string('value')->nullable(); // Optional: Hex code for color (e.g. #000000)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variations');
    }
};
