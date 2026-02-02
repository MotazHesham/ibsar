<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBeneficiaryCategoryIdToBeneficiariesTable extends Migration
{
    public function up()
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->unsignedBigInteger('beneficiary_category_id')->nullable();
            $table->foreign('beneficiary_category_id', 'beneficiary_category_fk')->references('id')->on('beneficiary_categories');
        });
    }

    public function down()
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropForeign('beneficiary_category_fk');
            $table->dropColumn('beneficiary_category_id');
        });
    }
}

