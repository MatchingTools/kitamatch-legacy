<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGuardiansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('guardians', function(Blueprint $table)
		{
			$table->increments('gid');
			$table->string('last_name', 60)->nullable();
			$table->string('first_name', 60)->nullable();
			$table->integer('uid')->unsigned()->unique('uid');
			$table->timestamps();
			$table->string('address', 60)->nullable();
			$table->string('city', 60)->nullable();
			$table->integer('plz')->unsigned()->nullable();
			$table->smallInteger('siblings')->unsigned()->nullable()->index('siblings');
			$table->smallInteger('parental_status')->unsigned()->nullable()->index('parental_status');
			$table->string('phone', 20)->nullable();
			$table->smallInteger('volume_of_employment')->unsigned()->nullable()->index('volume_of_employment');
			$table->smallInteger('status')->unsigned()->nullable()->index('status');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('guardians');
	}

}
