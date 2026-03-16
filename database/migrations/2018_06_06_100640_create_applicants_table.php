<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApplicantsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('applicants', function(Blueprint $table)
		{
			$table->increments('aid');
			$table->string('last_name', 20)->nullable();
			$table->string('first_name', 20)->nullable();
			$table->dateTime('birthday')->nullable();
			$table->string('gender', 10)->nullable();
			$table->smallInteger('status')->unsigned()->index('status');
			$table->integer('gid')->unsigned()->index('gid');
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
		Schema::drop('applicants');
	}

}
