<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateQueuesTable.
 */
class CreateQueuesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('queues', function(Blueprint $table) {
            $table->increments('id');
	    $table->string('uuid')->unique();
	    $table->string('status')->default(QUEUE_STATUS_WAITING);
	    $table->longText('payload')->nullable();
	    $table->longText('error')->nullable();


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
		Schema::drop('queues');
	}
}
