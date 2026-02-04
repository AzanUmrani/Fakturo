<?php

use App\Enums\QrCodeProvider;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pre_invoices', function (Blueprint $table) {
            $table->string('qr_provider')->default(QrCodeProvider::UNIVERSAL->value)->after('template_show_qr_payment');
        });
    }

    public function down(): void
    {
        Schema::table('pre_invoices', function (Blueprint $table) {
            $table->dropColumn('qr_provider');
        });
    }
};
