<?php

use Faker\Generator as Faker;


//Create only preferences for applicants
$factory->define(App\Preference::class, function (Faker $faker) {

  $idFrom = $faker->numberBetween($min = 1, $max = config('kitamatch_config.count_applicants'));
  $i = -1;
  while($i = -1) {
    $programId = $faker->numberBetween($min = 1, $max = config('kitamatch_config.count_programs'));
    $sql = "SELECT count(prid) AS count FROM `preferences` WHERE id_from = " .  $idFrom . " AND id_to = " . $programId;
    $preferenceCount = DB::select($sql);
    if ($preferenceCount{0}->count == 0) {
      $i = 1;
      break;
    }
  }

  return [
    //sample from the count sample applicants
    'id_from' => $idFrom,
    //sample to sample programs
    'id_to' => $programId,
    'status' => 1,
    'pr_kind' => 1,
    //could compute identical ranks for one applicant, are not sorted like 1,2,3,..; but still works with through the perference order
    'rank' => $faker->numberBetween($min = 1, $max = 20),
  ];
});
