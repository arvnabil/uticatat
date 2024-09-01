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
        Schema::create('bank_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('tanggal')->nullable();
            $table->string('bank')->nullable();
            $table->string('account_number')->nullable();
            $table->string('nominal')->nullable();
            $table->string('fee')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_receipts');
    }
};
