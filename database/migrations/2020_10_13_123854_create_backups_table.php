<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBackupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('database');
            $table->string('cron')->default('0 0 * * *');
            $table->string('path')->nullable();
            $table->string('destination')->nullable();
            $table->string('status')->default('never');
            $table->string('error')->nullable();
            $table->dateTime('run')->nullable();
            $table->unsignedInteger('rotate')->default(7);
            $table->unsignedInteger('database_id')->nullable();
            $table->unsignedInteger('domain_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('backups');
    }
}
