<?php

use Illuminate\Database\Seeder;

class ApplicantsTableSeeder extends Seeder
{
  /**
  * Run the database seeds.
  *
  * @return void
  */
  public function run() {
    factory('App\Applicant', config('kitamatch_config.count_applicants'))->create();
  }
}
