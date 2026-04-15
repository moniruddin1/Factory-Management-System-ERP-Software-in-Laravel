<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Update Productions Table
        Schema::table('productions', function (Blueprint $table) {
            $table->decimal('total_material_cost', 15, 2)->default(0)->after('total_cost');
            $table->decimal('labor_cost', 15, 2)->default(0)->after('total_material_cost');
            $table->decimal('overhead_cost', 15, 2)->default(0)->after('labor_cost');
            $table->decimal('final_total_cost', 15, 2)->default(0)->after('overhead_cost');
        });

        // Update Products Table
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('wholesale_price', 15, 2)->default(0)->after('selling_price');
            $table->decimal('retail_price', 15, 2)->default(0)->after('wholesale_price');
        });
    }

    public function down()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->dropColumn(['total_material_cost', 'labor_cost', 'overhead_cost', 'final_total_cost']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['wholesale_price', 'retail_price']);
        });
    }
};
