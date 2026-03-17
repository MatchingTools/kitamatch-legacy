<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProvidersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('providers', function(Blueprint $table)
		{
			$table->increments('proid');
			$table->timestamps();
			$table->string('name', 250)->nullable();
			$table->string('phone', 20)->nullable();
			$table->integer('uid')->unsigned()->nullable()->index('uid');
			$table->string('address', 60)->nullable();
			$table->integer('plz')->nullable();
			$table->string('city', 60)->nullable();
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
		Schema::drop('providers');
	}

}
