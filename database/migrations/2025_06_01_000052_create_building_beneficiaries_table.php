<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingBeneficiariesTable extends Migration
{
    public function up()
    {
        Schema::create('building_beneficiaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
