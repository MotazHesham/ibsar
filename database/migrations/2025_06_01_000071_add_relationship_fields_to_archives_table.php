<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToArchivesTable extends Migration
{
    public function up()
    {
        Schema::table('archives', function (Blueprint $table) {
            $table->unsignedBigInteger('archived_by_id')->nullable();
            $table->foreign('archived_by_id', 'archived_by_fk_10595319')->references('id')->on('users');
            $table->unsignedBigInteger('storage_location_id')->nullable();
            $table->foreign('storage_location_id', 'storage_location_fk_10595473')->references('id')->on('storage_locations');
        });
    }
}
