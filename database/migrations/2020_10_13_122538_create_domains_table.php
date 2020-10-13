<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('alias')->nullable();
            $table->string('user')->default('www');
            $table->string('type')->default('laravel');
            $table->string('certificate')->default('self-signed');
            $table->string('home')->default('public');
            $table->string('vcs_tye')->default('git');
            $table->string('vcs')->nullable();
            $table->string('deployer')->default('deploy');
            $table->unsignedInteger('database_id')->nullable();
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
        Schema::dropIfExists('domains');
    }
}
