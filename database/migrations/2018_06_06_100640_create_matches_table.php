<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMatchesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('matches', function(Blueprint $table)
		{
			$table->increments('mid');
			$table->integer('aid')->unsigned();
			$table->integer('pid')->unsigned()->index('pid');
			$table->timestamps();
			$table->smallInteger('status')->unsigned()->index('status');
			$table->index(['aid','pid'], 'aid');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('matches');
	}

}
