<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiariesTable extends Migration
{
    public function up()
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('form_step')->default('login_information');
            $table->string('profile_status')->default('uncompleted');
            $table->text('rejection_reason')->nullable();
            $table->date('dob')->nullable();
            $table->string('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('street')->nullable();
            $table->string('building_number')->nullable();
            $table->string('floor_number')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('building_additional_number')->nullable();
            $table->string('can_work')->nullable();
            $table->longText('incomes')->nullable();
            $table->decimal('total_incomes', 15, 2)->nullable();
            $table->longText('expenses')->nullable();
            $table->decimal('total_expenses', 15, 2)->nullable(); 
            $table->string('custom_health_condition')->nullable();
            $table->string('custom_disability_type')->nullable();
            $table->boolean('is_archived')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
