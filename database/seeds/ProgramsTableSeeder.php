<?php

use Illuminate\Database\Seeder;

class ProgramsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      //-- Random sample
      //factory('App\Program', config('kitamatch_config.count_programs'))->create();

      //-- Example sample (make sure count is same as in config!)
      $program = new App\Program();
      $program->name = "Sonnenschein - U3";
      $program->capacity = 5;
      $program->p_kind = 2;
      $program->coordination = 1;
      $program->status = 12;
      $program->uid = factory('App\User')->create(['email' => "sonnenschein.u3@zew.de", 'password' => Hash::make('secret'), 'account_type' => 2,])->id;
      $program->proid = 2;
      $program->save();

      $program = new App\Program();
      $program->name = "Sonnenschein - Ü3";
      $program->capacity = 5;
      $program->p_kind = 2;
      $program->coordination = 1;
      $program->status = 12;
      $program->uid = factory('App\User')->create(['email' => "sonnenschein.ue3@zew.de", 'password' => Hash::make('secret'), 'account_type' => 2,])->id;
      $program->proid = 2;
      $program->save();

      $program = new App\Program();
      $program->name = "DRK Burg Funkelstein";
      $program->capacity = 7;
      $program->p_kind = 2;
      $program->coordination = 1;
      $program->status = 12;
      $program->uid = factory('App\User')->create(['email' => "funkelstein@zew.de", 'password' => Hash::make('secret'), 'account_type' => 2,])->id;
      $program->save();

      $program = new App\Program();
      $program->name = "St. Marien";
      $program->capacity = 7;
      $program->p_kind = 2;
      $program->coordination = 1;
      $program->status = 12;
      $program->uid = factory('App\User')->create(['email' => "marien@zew.de", 'password' => Hash::make('secret'), 'account_type' => 2,])->id;
      $program->save();

      $program = new App\Program();
      $program->name = "Emilia";
      $program->capacity = 5;
      $program->p_kind = 2;
      $program->coordination = 1;
      $program->status = 12;
      $program->uid = factory('App\User')->create(['email' => "emilia@zew.de", 'password' => Hash::make('secret'), 'account_type' => 2,])->id;
      $program->save();

      $program = new App\Program();
      $program->name = "Kaleidoskop";
      $program->capacity = 8;
      $program->p_kind = 2;
      $program->coordination = 1;
      $program->status = 12;
      $program->uid = factory('App\User')->create(['email' => "kaleidoskop@zew.de", 'password' => Hash::make('secret'), 'account_type' => 2,])->id;
      $program->proid = 1;
      $program->save();
    }
}
