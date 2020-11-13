<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitors', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('webhook');
            $table->string('message');
            $table->string('to')->nullable();
            $table->string('monitor')->nullable();
            $table->string('constraint')->nullable();
            $table->string('cron')->default('0 * * * *');
            $table->string('error')->nullable();
            $table->boolean('alarm')->default(false);
            $table->dateTime('alarm_at')->nullable();
            $table->dateTime('alarm_until')->nullable();
            $table->string('rearm')->default('auto');

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
        Schema::dropIfExists('monitors');
    }
}
