<?php

use App\Enums\InvoiceHistoryTypeEnum;
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
        Schema::create('pre_invoice_histories', function (Blueprint $table) {
            $table->id();
            $table->uuid()->index()->unique();
            $table->unsignedBigInteger('pre_invoice_id');
            $table->foreign('pre_invoice_id')
                ->references('id')
                ->on('pre_invoices')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->enum('type', InvoiceHistoryTypeEnum::toArrayStrings());

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_invoice_histories');
    }
};
