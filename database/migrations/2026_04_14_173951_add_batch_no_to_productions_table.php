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
        Schema::table('productions', function (Blueprint $table) {
            // reference_no এর পরে batch_no কলামটি যোগ করছি
            $table->string('batch_no')->nullable()->after('reference_no');
        });
    }

    public function down()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->dropColumn('batch_no');
        });
    }

    /**
     * Reverse the migrations.
     */

};
