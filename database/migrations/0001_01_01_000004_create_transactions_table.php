<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id', 'transactions_user_id_fk')->references('id')->on('users');
            $table->bigInteger('amount');
            $table->string('type')->index(); // debit or credit
            $table->text('note')->nullable();
            $table->unsignedBigInteger('sheba_request_id')->nullable()->index();
            $table->foreign('sheba_request_id', 'transactions_sheba_request_id_fk')->references('id')->on('sheba_requests');
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
}; 