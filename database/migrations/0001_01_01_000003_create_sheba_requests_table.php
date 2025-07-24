<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sheba_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id', 'sheba_requests_user_id_fk')->references('id')->on('users');
            $table->bigInteger('price');
            $table->string('status')->default('pending')->index();
            $table->string('fromShebaNumber', 26);
            $table->string('toShebaNumber', 26);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sheba_requests');
    }
}; 