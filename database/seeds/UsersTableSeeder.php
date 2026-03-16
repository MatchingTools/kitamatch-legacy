<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      factory('App\User', config('kitamatch_config.count_users'))->create();

      //create super / admin user
      $user = new App\User();
      $user->password = Hash::make('secret');
      $user->email = 'm@zew.de';
      $user->account_type = "5";
      $user->save();
    }
}
