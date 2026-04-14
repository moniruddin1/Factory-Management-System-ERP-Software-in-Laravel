<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();

            // কী ধরনের ট্রানজেকশন হচ্ছে
            $table->string('transaction_type'); // Purchase, Purchase Return, Transfer In, Transfer Out, Production Issue, Production Receive, Sales, Sales Return

            // কোন মডিউল থেকে ট্রানজেকশন আসছে (Polymorphic Relation এর মতো)
            $table->string('reference_type')->nullable(); // e.g., App\Models\Purchase
            $table->unsignedBigInteger('reference_id')->nullable(); // Purchase ID

            $table->decimal('quantity', 10, 2); // (+) মানে ইন, (-) মানে আউট
            $table->decimal('unit_cost', 10, 2)->default(0.00); // ওই সময় মালের দাম

            // কে এন্ট্রি দিয়েছে
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
