<?php

return [

  // -----------------------------
  'show_gender' => FALSE,
  // -----------------------------
  'can_user_change_capacity' => TRUE,
  // -----------------------------

  // MANUEL POINTS
  'manual_points' => TRUE,
  'manual_points_value' => 10,
  // -----------------------------

  // ADDITIONAL BONUS FOR FIRST PREFERENCE KITA
  'preference_bouns' => TRUE,
  'preference_bouns_value' => 3,
  // -----------------------------

  // ADDITIONAL CRITERIA FOR BONUS (EX. PARENTS OCCUPATION, RELIGION, REGIONAL )
  'additionalCriteriaBonus_1' => TRUE,
  'additionalCriteriaBonus_1_value' => 9,
  // -----------------------------

  // ADDITIONAL CRITERIA FOR BONUS (EX. PARENTS OCCUPATION, RELIGION, REGIONAL )
  'additionalCriteriaBonus_2' => FALSE,
  'additionalCriteriaBonus_2_value' => 6,
  // -----------------------------

  // ADDITIONAL CRITERIA FOR BONUS (EX. PARENTS OCCUPATION, RELIGION, REGIONAL )  
  'additionalCriteriaBonus_3' => FALSE,
  'additionalCriteriaBonus_3_value' => 3,
  // -----------------------------

  // ADDITIONAL CRITERIA FOR BONUS (EX. PARENTS OCCUPATION, RELIGION, REGIONAL )  
  'additionalCriteriaBonus_4' => FALSE,
  'additionalCriteriaBonus_4_value' => 6,
  // -----------------------------

  // ADDITIONAL CRITERIA FOR BONUS (EX. PARENTS OCCUPATION, RELIGION, REGIONAL )  
  'additionalCriteriaBonus_5' => FALSE,
  'additionalCriteriaBonus_5_value' => 6,
  // -----------------------------

  // ADDITIONAL CRITERIA FOR BONUS (EX. PARENTS OCCUPATION, RELIGION, REGIONAL )  
  'additionalCriteriaBonus_6' => FALSE,
  'additionalCriteriaBonus_6_value' => 6,
  // -----------------------------

  // ADDITIONAL CRITERIA FOR BONUS (EX. PARENTS OCCUPATION, RELIGION, REGIONAL )  
  'additionalCriteriaBonus_7' => FALSE,
  'additionalCriteriaBonus_7_value' => 6,
  // -----------------------------

  // ADDITIONAL CRITERIA FOR BONUS (EX. PARENTS OCCUPATION, RELIGION, REGIONAL )  
  'additionalCriteriaBonus_8' => FALSE,
  'additionalCriteriaBonus_8_value' => 6,
  // -----------------------------

  // ADDITIONAL CRITERIA FOR BONUS (EX. PARENTS OCCUPATION, RELIGION, REGIONAL )  
  'additionalCriteriaBonus_9' => FALSE,
  'additionalCriteriaBonus_9_value' => 6,
  // -----------------------------

  // ADDITIONAL CRITERIA FOR BONUS (EX. PARENTS OCCUPATION, RELIGION, REGIONAL )  
  'additionalCriteriaBonus_10' => FALSE,
  'additionalCriteriaBonus_10_value' => 6,
  // -----------------------------

  // ADDITIONAL CRITERIA FOR BONUS (EX. PARENTS OCCUPATION, RELIGION, REGIONAL )  
  'additionalCriteriaBonus_11' => FALSE,
  'additionalCriteriaBonus_11_value' => 6,
  // -----------------------------

  // ADDITIONAL CRITERIA FOR BONUS (EX. PARENTS OCCUPATION, RELIGION, REGIONAL )  
  'additionalCriteriaBonus_12' => FALSE,
  'additionalCriteriaBonus_12_value' => 6,
  // -----------------------------
  
  // DIFFERENT STARTS?
  'has_diff_starts' => TRUE,
  'has_diff_scopes' => TRUE,

  // CARE STARTES
  'care_starts' => [
    -1 => 'Bitte auswählen...',
    1 => 'Q1',
    2 => 'Q2',
    3 => 'Q3',
    4 => 'Q4'
  ],

  'care_starts_text' => [
    'question_select' => 'Welches ist der für Sie frühestmögliche akzeptable Betreuungsbeginn?',
    'question_bool' => 'Wären Sie bereit, mindestens 3 Monate auf ihre Wunschkita zu warten, wenn dort zum Wunschzeitpunkt noch kein Platz frei ist?'
  ],

  // CARE SCOPES
  'care_scopes' => [
    -1 => 'Bitte auswählen...',
    1 => 'Halbtags',
    2 => 'Ganztags'
  ],

  'care_scopes_text' => [
    'question_select' => 'Präferieren Sie Halbtag oder Ganztag?',
    'question_bool' => 'Ist für Sie grundsätzlich der andere Betreuungsumfang auch akzeptabel?'
  ],

  // AGE COHORTS
  'age_cohorts' => [
    0 => '---',
    1 => 'U2',
    2  => '2',
    3 => 'Ü3'
  ],

  // -----------------------------
  // -----------------------------
  // -----------------------------


  //general, register, coordination, finished
  'stage' => 'general',

  //'coordination_start_date' => '2018-04-20'
  // defines date coordiantion phase starts, gives all private programs a 7 days chance afterwards to be active
  'coordination_start_date' => date('Y-m-d H:i:s'),

  //seeds
  'count_applicants' => 65,
  'count_programs' => 6,
  'count_providers' => 2,
  'count_guardians' => 65,
  'count_users' => 10,
];
