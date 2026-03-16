<?php

use Faker\Generator as Faker;

$factory->define(App\Program::class, function (Faker $faker) {
  /*$random = rand(1, 10);
  //70% for public, 30% for private programs
  if ($random <= 7) {
    //public
    $p_kind = 1;
    $coordination = 1;
  } else {
    //privaten
    $p_kind = 2;
    $coordination = 0;
  }*/

  $p_kind = 1;
  $coordination = 1;

  return [
    'name' => $faker->company,
    'phone' => $faker->e164PhoneNumber,
    'address' => $faker->streetName . " " . $faker->buildingNumber,
    'plz' => $faker->numberBetween($min = 30000, $max = 90000),
    'city' => $faker->city,
    'status' => 12,
    'uid' => factory('App\User')->create()->id,
    'capacity' => $faker->numberBetween($min = 3, $max = 10),
    'p_kind' => $p_kind,
    'coordination' => $coordination,
    //tmp: no provider
    //proid
  ];
});
