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
        Schema::create('mailbox_message_participants', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('message_id');
            $table->foreign('message_id', 'message_fk_13124501')->references('id')->on('mailbox_messages');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'user_fk_1013411')->references('id')->on('users');

            $table->string('role');

            $table->boolean('is_read')->default(false);
            $table->boolean('is_starred')->default(false);
            $table->boolean('is_important')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();

            $table->unique(['message_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mailbox_message_participants');
    }
};
