<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEconomicStatusesTable extends Migration
{
    public function up()
    {
        Schema::create('economic_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('type');
            $table->string('data_type');
            $table->boolean('is_required')->default(false);
            $table->integer('order_level')->default(0); 
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
