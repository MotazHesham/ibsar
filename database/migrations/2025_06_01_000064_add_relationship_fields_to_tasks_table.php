<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToTasksTable extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable();
            $table->foreign('status_id', 'status_fk_10588501')->references('id')->on('task_statuses');
            $table->unsignedBigInteger('task_priority_id')->nullable();
            $table->foreign('task_priority_id', 'task_priority_fk_10588608')->references('id')->on('task_priorities');
            $table->unsignedBigInteger('task_board_id')->nullable();
            $table->foreign('task_board_id', 'task_board_fk_10588568')->references('id')->on('task_boards');
            $table->unsignedBigInteger('assigned_by_id')->nullable();
            $table->foreign('assigned_by_id', 'assigned_by_fk_10588569')->references('id')->on('users');
        });
    }
}
