<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('identity_num');
            $table->string('email');
            $table->string('password')->nullable();
            $table->string('phone_number');
            $table->string('interest')->nullable();
            $table->string('initiative_name')->nullable();
            $table->string('prev_experience')->nullable();
            $table->tinyInteger('approved')->default(0);
            $table->datetime('email_verified_at')->nullable();
            $table->string('remember_token')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('volunteer_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('identity');
            $table->string('address');
            $table->string('phone');
            $table->longText('details')->nullable();
            $table->string('visit_type');
            $table->date('date');
            $table->time('arrive_time')->nullable();
            $table->time('leave_time')->nullable();
            $table->string('status')->default('pending');
            $table->longText('cancel_reason')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('volunteer_id')->nullable();
            $table->foreign('volunteer_id', 'volunteer_fk_10191121')->references('id')->on('volunteers');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_tasks');
        Schema::dropIfExists('volunteers');
    }
};
