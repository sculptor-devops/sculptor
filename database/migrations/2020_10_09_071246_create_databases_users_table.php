<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatabasesUsersTable extends Migration
{
    /**
     * System the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('database_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('database_id');
            $table->string('name', 30)->index()->unique();
            $table->string('host', 250)->index();
            $table->string('password', 255)->nullable();
            $table->timestamps();

            $table->unique(['database_id', 'name' ]);
            $table->foreign('database_id')->references('id')->on('databases');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('database_users');
    }
}
