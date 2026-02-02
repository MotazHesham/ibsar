<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskUserPivotTable extends Migration
{
    public function up()
    {
        Schema::create('task_user', function (Blueprint $table) {
            $table->unsignedBigInteger('task_id');
            $table->foreign('task_id', 'task_id_fk_10588509')->references('id')->on('tasks')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'user_id_fk_10588509')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
