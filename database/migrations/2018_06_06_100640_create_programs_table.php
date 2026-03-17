<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProgramsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('programs', function(Blueprint $table)
		{
			$table->increments('pid');
			$table->string('name', 250)->nullable();
			$table->string('address', 60)->nullable();
			$table->smallInteger('capacity')->unsigned()->nullable();
			$table->boolean('p_kind')->nullable();
			$table->smallInteger('status')->unsigned()->index('status');
			$table->boolean('coordination')->nullable();
			$table->integer('uid')->unsigned()->nullable()->index('uid');
			$table->integer('proid')->unsigned()->nullable()->index('proid');
			$table->string('phone', 20)->nullable();
			$table->integer('plz')->unsigned()->nullable();
			$table->string('city', 60)->nullable();
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
		Schema::drop('programs');
	}

}
