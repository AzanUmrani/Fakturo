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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid()->index()->unique();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('role')->default('user');
            $table->rememberToken();

            $table->string('apple_token')->nullable();
            $table->text('apple_extra')->nullable();

            $table->string('google_token')->nullable();
            $table->text('google_extra')->nullable();

            $table->string('facebook_token')->nullable();
            $table->text('facebook_extra')->nullable();

            $table->integer('invoice_count')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
