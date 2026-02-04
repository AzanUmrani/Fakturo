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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->enum('type', ['product', 'service'])->default('service');
            $table->string('name');
            $table->text('description')->nullable();
            $table->float('price');
            $table->float('taxRate')->nullable();
            $table->float('discount')->default(0);

            $table->string('unit')->nullable();
            $table->string('sku')->nullable();
            $table->float('weight')->nullable();

            $table->boolean('has_image')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
