<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomingLettersTable extends Migration
{
    public function up()
    {
        Schema::create('incoming_letters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('letter_number')->nullable();
            $table->string('external_number')->nullable();
            $table->date('letter_date');
            $table->date('received_date');
            $table->string('subject');
            $table->longText('letter');
            $table->string('priority');
            $table->string('incoming_type');
            $table->longText('note')->nullable();
            $table->boolean('is_archived')->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
