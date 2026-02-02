<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToStorageLocationsTable extends Migration
{
    public function up()
    {
        Schema::table('storage_locations', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id', 'parent_fk_10595462')->references('id')->on('storage_locations');
        });
    }
}
