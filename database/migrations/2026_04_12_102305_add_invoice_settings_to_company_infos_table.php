<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_infos', function (Blueprint $table) {
            $table->string('invoice_title')->default('INVOICE')->after('logo');
            $table->string('invoice_watermark_logo')->nullable()->after('invoice_title');
            $table->string('invoice_watermark_text')->nullable()->after('invoice_watermark_logo');
            $table->text('invoice_note')->nullable()->after('invoice_watermark_text');
        });
    }

    public function down(): void
    {
        Schema::table('company_infos', function (Blueprint $table) {
            $table->dropColumn(['invoice_title', 'invoice_watermark_logo', 'invoice_watermark_text', 'invoice_note']);
        });
    }
};
