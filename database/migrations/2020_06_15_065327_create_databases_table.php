<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateDatabasesTable.
 */
class CreateDatabasesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('databases', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30)->unique()->index();
            $table->string('driver', 30)->default('mysql');

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
		Schema::drop('databases');
	}
}
