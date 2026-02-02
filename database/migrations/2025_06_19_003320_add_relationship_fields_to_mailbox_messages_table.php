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
        Schema::table('mailbox_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->foreign('sender_id', 'sender_fk_10588501')->references('id')->on('users');  

            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id', 'parent_fk_10588501')->references('id')->on('mailbox_messages');

            $table->unsignedBigInteger('thread_id')->nullable();
            $table->foreign('thread_id', 'thread_fk_10588501')->references('id')->on('mailbox_messages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mailbox_messages', function (Blueprint $table) {
            //
        });
    }
};
