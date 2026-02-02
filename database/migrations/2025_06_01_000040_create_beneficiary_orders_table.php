<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiaryOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('beneficiary_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('service_type')->nullable();
            $table->string('title')->nullable();
            $table->longText('description');
            $table->string('accept_status')->nullable();
            $table->longText('note')->nullable();
            $table->longText('refused_reason')->nullable();
            $table->boolean('done')->default(0);
            $table->boolean('is_archived')->default(0);
            $table->string('handle')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
