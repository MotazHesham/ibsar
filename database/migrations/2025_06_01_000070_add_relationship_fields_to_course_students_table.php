<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToCourseStudentsTable extends Migration
{
    public function up()
    {
        Schema::table('course_students', function (Blueprint $table) {
            $table->unsignedBigInteger('beneficiary_order_id')->nullable();
            $table->foreign('beneficiary_order_id', 'beneficiary_order_fk_10595292')->references('id')->on('beneficiary_orders');
            $table->unsignedBigInteger('course_id')->nullable();
            $table->foreign('course_id', 'course_fk_10595292')->references('id')->on('courses');
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->foreign('beneficiary_id', 'beneficiary_fk_10595293')->references('id')->on('beneficiaries');
            $table->unsignedBigInteger('beneficiary_family_id')->nullable();
            $table->foreign('beneficiary_family_id', 'beneficiary_family_fk_10595294')->references('id')->on('beneficiary_families');
        });
    }
}
