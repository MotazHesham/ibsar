<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToBeneficiaryFilesTable extends Migration
{
    public function up()
    {
        Schema::table('beneficiary_files', function (Blueprint $table) {
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->foreign('beneficiary_id', 'beneficiary_fk_10586775')->references('id')->on('beneficiaries');
            $table->unsignedBigInteger('required_document_id')->nullable();
            $table->foreign('required_document_id', 'required_document_fk_10586778')->references('id')->on('required_documents');
        });
    }
}
