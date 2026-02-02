<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToIncomingLettersTable extends Migration
{
    public function up()
    {
        Schema::table('incoming_letters', function (Blueprint $table) {
            $table->unsignedBigInteger('recevier_id')->nullable();
            $table->foreign('recevier_id', 'recevier_fk_10591987')->references('id')->on('users');
            $table->unsignedBigInteger('letter_organization_id')->nullable();
            $table->foreign('letter_organization_id', 'letter_organization_fk_10592036')->references('id')->on('letters_organizations');
            $table->unsignedBigInteger('outgoing_letter_id')->nullable();
            $table->foreign('outgoing_letter_id', 'outgoing_letter_fk_10592084')->references('id')->on('outgoing_letters');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->foreign('created_by_id', 'created_by_fk_10592085')->references('id')->on('users');
        });
    }
}
