<?php

use Illuminate\Database\Seeder;

class GuardiansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory('App\Guardian', config('kitamatch_config.count_guardians'))->create();
    }
}
