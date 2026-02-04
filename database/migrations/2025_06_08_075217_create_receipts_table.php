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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('billed_to_client_id')->constrained('clients')->onDelete('cascade');
            $table->string('language_2_code', 2)->nullable();
            $table->decimal('total', 15, 2);
            $table->string('currency_3_code', 3);
            $table->text('purpose')->nullable();
            $table->string('made_by')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('journal_number')->nullable();
            $table->json('billing_regulation')->nullable();
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
