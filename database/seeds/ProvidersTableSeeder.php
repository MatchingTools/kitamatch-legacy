<?php

use Illuminate\Database\Seeder;

class ProvidersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      //-- Random sample
      //factory('App\Provider', config('kitamatch_config.count_providers'))->create();

      //-- Example sample
      $provider = new App\Provider();
      $provider->name = 'Stadt';
      $provider->phone = '+4915776638369';
      $provider->plz = "69115";
      $provider->city = "Heidelberg";
      $provider->address = "Bismarckplatz 10";
      $provider->status = 61;
      $provider->uid = factory('App\User')->create(['email' => "stadt@zew.de", 'password' => Hash::make('secret'), 'account_type' => 4,])->id;
      $provider->save();

      $provider = new App\Provider();
      $provider->name = 'Sonnenschein';
      $provider->phone = '+4915776638369';
      $provider->plz = "69115";
      $provider->city = "Heidelberg";
      $provider->address = "Bismarckplatz 10";
      $provider->status = 61;
      $provider->uid = factory('App\User')->create(['email' => "sonnenschein@zew.de", 'password' => Hash::make('secret'), 'account_type' => 4,])->id;
      $provider->save();
    }
}
