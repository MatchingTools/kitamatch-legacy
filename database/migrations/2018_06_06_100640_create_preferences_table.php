<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePreferencesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('preferences', function(Blueprint $table)
		{
			$table->increments('prid');
			$table->integer('id_from')->unsigned()->index('id_from');
			$table->integer('id_to')->unsigned()->index('id_to');
			$table->boolean('pr_kind');
			$table->timestamps();
			$table->smallInteger('rank');
			$table->boolean('status')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('preferences');
	}

}
