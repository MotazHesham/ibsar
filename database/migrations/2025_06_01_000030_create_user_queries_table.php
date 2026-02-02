<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserQueriesTable extends Migration
{
    public function up()
    {
        Schema::create('user_queries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('question');
            $table->longText('answer')->nullable();
            $table->timestamps();
        });
    }
}
