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
        Schema::create('conversation_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('unread_count')->default(0);
            $table->unsignedBigInteger('conversation_id');
            $table->foreign('conversation_id', 'conversation_fk_10595475')->references('id')->on('conversations');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'user_fk_10595476')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_user');
    }
};
