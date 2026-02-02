<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToOutgoingLettersTable extends Migration
{
    public function up()
    {
        Schema::table('outgoing_letters', function (Blueprint $table) {
            $table->unsignedBigInteger('recevier_id')->nullable();
            $table->foreign('recevier_id', 'recevier_fk_10592070')->references('id')->on('users');
            $table->unsignedBigInteger('incoming_letter_id')->nullable();
            $table->foreign('incoming_letter_id', 'incoming_letter_fk_10592086')->references('id')->on('incoming_letters');
            $table->unsignedBigInteger('letter_organization_id')->nullable();
            $table->foreign('letter_organization_id', 'letter_organization_fk_10542336')->references('id')->on('letters_organizations');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->foreign('created_by_id', 'created_by_fk_10592087')->references('id')->on('users');
        });
    }
}
