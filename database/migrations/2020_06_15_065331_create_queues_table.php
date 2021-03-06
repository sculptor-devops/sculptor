<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Sculptor\Agent\Enums\QueueStatusType;

/**
 * Class CreateQueuesTable.
 */
class CreateQueuesTable extends Migration
{
	/**
	 * System the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('queues', function(Blueprint $table) {
            $table->increments('id');
	        $table->string('uuid')->unique();
            $table->string('type');
            $table->string('status')->default(QueueStatusType::WAITING);
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
