<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchivesTable extends Migration
{
    public function up()
    {
        Schema::create('archives', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('archived_at')->nullable();
            $table->longText('archive_reason')->nullable();
            $table->longText('metadata')->nullable();
            $table->nullableMorphs('archiveable');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
