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
        Schema::create('pre_invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid()->index()->unique();
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->string('number');
            $table->date('billed_date');
            $table->date('due_date');
            $table->date('send_date');

            $table->string('variable_symbol')->nullable();
            $table->string('constant_symbol')->nullable();
            $table->string('specific_symbol')->nullable();

            $table->string('order_id')->nullable();

            $table->json('billed_from_client');
            $table->json('billed_to_client');
            $table->json('items');

            $table->string('payment');
            $table->json('bank_transfer')->nullable();

            $table->string('note')->nullable();

            $table->float('totalPrice'); /* TODO change to total_price ... */
            $table->float('cash_payment_rounding')->default(0);
            $table->string('currency_3_code');
            $table->string('language_2_code');
            $table->string('template')->default('Sango');
            $table->string('template_primary_color')->default('#b23a5a');
            $table->string('template_date_format')->default('d.m.Y');
            $table->string('template_price_decimal_format')->default('.');
            $table->string('template_price_thousands_format')->default(',');

            $table->boolean('template_show_quantity')->default(true);
            $table->boolean('template_show_due_date')->default(true);
            $table->boolean('template_show_send_date')->default(true);
            $table->boolean('template_show_payment')->default(true);
            $table->boolean('template_show_qr_payment')->default(true);

            $table->boolean('paid')->default(false);
            $table->boolean('sent')->default(false);
            $table->boolean('open')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_invoice');
    }
};
