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
        Schema::create('user_marital_status_pivot', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'user_id_fk_10242422')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('marital_status_id');
            $table->foreign('marital_status_id', 'marital_status_id_fk_1052522')->references('id')->on('marital_statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_marital_status_pivot');
    }
};
