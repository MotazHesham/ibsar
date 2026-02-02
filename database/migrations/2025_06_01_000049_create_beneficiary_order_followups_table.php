<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiaryOrderFollowupsTable extends Migration
{
    public function up()
    {
        Schema::create('beneficiary_order_followups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('comment');
            $table->date('date')->nullable(); 
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
