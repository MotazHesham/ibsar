<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFamilyRelationshipsTable extends Migration
{
    public function up()
    {
        Schema::create('family_relationships', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('gender');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
