<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequiredDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('required_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name'); 
            $table->boolean('is_required')->default(false); 
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
