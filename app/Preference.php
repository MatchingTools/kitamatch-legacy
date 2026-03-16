<?php
/*
 * This file is part of the KitaMatch app.
 *
 * (c) Sven Giegerich <sven.giegerich@mailbox.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
 /*
 |--------------------------------------------------------------------------
 | Preference Model
 |--------------------------------------------------------------------------
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Program;

/**
* This model handles the preferences of programs and applicants
*/
class Preference extends Model
{
  //pr_kind: 1:applicant, 2:program coordinated, 3:program uncoordinated

  /**
  * Update the status of a single preference
  *
  * @param integer $prid Preference-ID
  * @param integer $status status
  * @return void
  */
  public function updateStatus($prid, $status) {
    $exec = DB::table('preferences')
      ->where('prid', '=', $prid)
      ->update(array('status' => $status));
  }

  /**
  * Update the rank of a single preference
  *
  * @param integer $prid Preference-ID
  * @param integer $rank rank
  * @return void
  */
  public function updateRank($prid, $rank) {
    $exec = DB::table('preferences')
      ->where('prid', '=', $prid)
      ->update(array('rank' => $rank));
  }

  /**
  * Reset (status = -1) all offers (rank = 1) created by uncoordianted programs. This method is called during the findMatchings() process, to differ between successfull and denied offers
  *
  * @return void
  */
  public function resetUncoordinatedOffers() {
    $nonactive = DB::table('preferences')
      ->where('preferences.pr_kind', 3)
      ->where('preferences.rank', '=', 1)
      ->where('preferences.status', '=', 1) //reset only valid offers
      ->join('applicants', function ($join) {
        $join->on('preferences.id_to', '=', 'applicants.aid')
        ->whereIn('applicants.status', [22, 25]);
      })
      ->update(array('preferences.status' => -1));
  }

  public function resetAllUncoordnatedQueuesByApplicant($aid, $pid) {
    $nonactive = DB::table('preferences')
      ->where('pr_kind', '=', 3)
      ->where('rank', '>', 1)
      ->where('id_to', '=', $aid)
      ->where('id_from', '!=', $pid)
      ->update(array('status' => -1));
  }

  /**
  * Does the program has any active (status = 1) preferences?
  *
  * @param integer $pid Program-ID
  * @return boolean
  */
  public function hasPreferencesByProgram($pid) {
    $preferenceProgram = Preference::where('id_from', '=', $pid)
      ->where('status', '>', 0)
      ->whereIn('pr_kind', [2, 3])
      ->first();
    if ($preferenceProgram === null) {
      return false;
    } else {
      return true;
    }
  }

  /**
  * Does the applicant has any active (status = 1) preferences?
  *
  * @param integer $aid Applicant-ID
  * @param boolean
  */
  public function hasPreferencesByApplicant($aid) {
    $preferenceApplicant = Preference::where('id_from', '=', $aid)
      ->where('status', '>', 0)
      ->where('pr_kind', 1)
      ->first();
    if ($preferenceApplicant === null) {
      return 0;
    } else {
      return 1;
    }
  }

  /**
  * Get all applicants that are available for a program (because the applicant has ranked this program)
  *
  * @param integer $pid Program-ID
  * @return Illuminate\Database\Eloquent\Collection applicants
  */
  public function getAvailableApplicants($pid) {
    $applicants = DB::table('preferences')
      ->join('applicants', 'applicants.aid', '=', 'preferences.id_from')
      ->where('preferences.program_id', '=', $pid)
      ->whereIn('preferences.status',  [1, -1])
      ->where('preferences.pr_kind', '=', 1)
      ->select('applicants.*')
      ->distinct()
      ->get();

    return $applicants;
  }

  public function getPreferencesByApplicant($aid, $pid){
    $preferences =DB::table('preferences')
      ->where('preferences.id_from', '=', $aid)
      ->where('preferences.program_id','=', $pid)
      ->where('preferences.status', '=', 1)
      ->where('preferences.pr_kind', '=', 1)
      ->get();

    return $preferences;
  }

  public function getAllPreferencesByApplicantID($aid){
    $preferences =DB::table('preferences')
      ->where('preferences.id_from', '=', $aid)
      ->where('preferences.status', '=', 1)
      ->where('preferences.pr_kind', '=', 1)
      ->get();

    return $preferences;
  }

  public function getPreferenceDetails($college, $student){
    $preference =DB::table('preferences')
      ->where('preferences.id_from', '=', $student)
      ->where('preferences.id_to', '=', $college)
      ->where('preferences.status', '=', 1)
      ->where('preferences.pr_kind', '=', 1)
      ->get();

    return $preference;
  }

  public function getPreferenceByApplicantAndSid($aid, $sid){
    $preference =DB::table('preferences')
    ->where('preferences.id_from', '=', $aid)
    ->where('preferences.program_id','=', $sid)
    ->where('preferences.status', '=', 1)
    ->where('preferences.pr_kind', '=', 1)
    ->get();

    return $preference;
  }

  public function getOfferedPreference($id_to, $aid){
    $offeredPreference =DB::table('preferences')
      ->where('preferences.id_from','like', $id_to)
      ->where('preferences.id_to', '=', $aid)
      ->whereIn('preferences.status', [1, -1])
      ->get();

    return $offeredPreference;
  }

  public function getCurrentOfferOfScope($id_to){
    $currentOffer =DB::table('preferences')
      ->where('preferences.id_from', 'like', $id_to)
      ->where('preferences.status', '=', 1)
      ->whereIn('pr_kind', [2, 3])
      ->get();

    return $currentOffer;
  }

  /**
  * Takes an array of applicants and sorts them after the corresponding criteria catalogue of the provider or program.
  * Adds an additional order attribute to every entry containing the criteria score
  *
  * @param App\Applicant $applicants applicants
  * @param integer $p_id Program/Provider-ID
  * @param boolean $provider IsProvider?
  * @return Illuminate\Database\Eloquent\Collection applicants ordered, and with order-attribute (correspoonding criteria-points)
  */
  public function orderByCriteria($applicants, $p_id, $provider) {
    $Program = new Program();

    //$provider = true -> criteria from a provider level
    if ($provider) {
      $criteria = Criterium::where('p_id', '=', $p_id)
        ->orderBy('rank', 'asc')
        ->get();
      $provider_id = $p_id;
    } else {
      //single program
      $criteria = Criterium::where('p_id', '=', $p_id)
        ->where('program', '=', 1)
        ->orderBy('rank', 'asc')
        ->get();
      $povider_id = $Program->getProviderId($p_id);
    }

    //if criteria is null, use the default order (indicated by providerId = -1)
    if (!(count($criteria)>0)) {
      $criteria = Criterium::where('p_id', '=', -1)
      ->orderBy('rank', 'asc')
      ->get();
    }

    // 1. add order tag
    foreach($applicants as $applicant) {
      //$guardian = Guardian::find($applicant->gid);
      $applicant->order = 0;
      //if ($guardian != null) {
        foreach($criteria as $criterium) {
          $criterium_name = $criterium->criterium_name;
          if ($criterium->criterium_value == $applicant->{$criterium_name}) {
            $applicant->order = $applicant->order + $criterium->rank * $criterium->multiplier;
          }
        }

      // if manual points = TRUE, calculate points if sibiling is within the same institution
      if (config('kitamatch_config.manual_points')) {
        if ($applicant->siblings == $provider_id) {
          $applicant->points = $applicant->points_manual + config('kitamatch_config.manual_points_value');
        } else {
          $applicant->points = $applicant->points_manual;
        }
      }

      //Bonus for first prefered kita
      if(config('kitamatch_config.preference_bouns')){
        if ($applicant->first_preference_program == $provider_id) {
          $applicant->points = $applicant->points + config('kitamatch_config.preference_bouns_value');
        } else {
          $applicant->points = $applicant->points;
        }
      }

      //Additional Bonus for applicants
      if(config('kitamatch_config.additionalCriteriaBonus_1')){
        if ($applicant->additionalCriteria_1 == $provider_id) {
          $applicant->points = $applicant->points + config('kitamatch_config.additionalCriteriaBonus_1_value');
        } else {
          $applicant->points = $applicant->points;
        }
      }

      //Additional Bonus for applicants
      if(config('kitamatch_config.additionalCriteriaBonus_2')){
        if ($applicant->additionalCriteria_2 == $provider_id) {
          $applicant->points = $applicant->points + config('kitamatch_config.additionalCriteriaBonus_2_value');
        } else {
          $applicant->points = $applicant->points;
        }
      }
      
      //Additional Bonus for applicants
      if(config('kitamatch_config.additionalCriteriaBonus_3')){
        if ($applicant->additionalCriteria_3 == $provider_id) {
          $applicant->points = $applicant->points + config('kitamatch_config.additionalCriteriaBonus_3_value');
        } else {
          $applicant->points = $applicant->points;
        }
      }

      //Additional Bonus for applicants
      if(config('kitamatch_config.additionalCriteriaBonus_4')){
        if ($applicant->additionalCriteria_4 == $provider_id) {
          $applicant->points = $applicant->points + config('kitamatch_config.additionalCriteriaBonus_4_value');
        } else {
          $applicant->points = $applicant->points;
        }
      }

      //Additional Bonus for applicants
      if(config('kitamatch_config.additionalCriteriaBonus_5')){
        if ($applicant->additionalCriteria_5 == $provider_id) {
          $applicant->points = $applicant->points + config('kitamatch_config.additionalCriteriaBonus_5_value');
        } else {
          $applicant->points = $applicant->points;
        }
      }

      //Additional Bonus for applicants
      if(config('kitamatch_config.additionalCriteriaBonus_6')){
        if ($applicant->additionalCriteria_6 == $provider_id) {
          $applicant->points = $applicant->points + config('kitamatch_config.additionalCriteriaBonus_6_value');
        } else {
          $applicant->points = $applicant->points;
        }
      }

      //Additional Bonus for applicants
      if(config('kitamatch_config.additionalCriteriaBonus_7')){
        if ($applicant->additionalCriteria_7 == $provider_id) {
          $applicant->points = $applicant->points + config('kitamatch_config.additionalCriteriaBonus_7_value');
        } else {
          $applicant->points = $applicant->points;
        }
      }

      //Additional Bonus for applicants
      if(config('kitamatch_config.additionalCriteriaBonus_8')){
        if ($applicant->additionalCriteria_8 == $provider_id) {
          $applicant->points = $applicant->points + config('kitamatch_config.additionalCriteriaBonus_8_value');
        } else {
          $applicant->points = $applicant->points;
        }
      }

      //Additional Bonus for applicants
      if(config('kitamatch_config.additionalCriteriaBonus_9')){
        if ($applicant->additionalCriteria_9 == $provider_id) {
          $applicant->points = $applicant->points + config('kitamatch_config.additionalCriteriaBonus_9_value');
        } else {
          $applicant->points = $applicant->points;
        }
      }

      //Additional Bonus for applicants
      if(config('kitamatch_config.additionalCriteriaBonus_10')){
        if ($applicant->additionalCriteria_10 == $provider_id) {
          $applicant->points = $applicant->points + config('kitamatch_config.additionalCriteriaBonus_10_value');
        } else {
          $applicant->points = $applicant->points;
        }
      }

      //Additional Bonus for applicants
      if(config('kitamatch_config.additionalCriteriaBonus_11')){
        if ($applicant->additionalCriteria_11 == $provider_id) {
          $applicant->points = $applicant->points + config('kitamatch_config.additionalCriteriaBonus_11_value');
        } else {
          $applicant->points = $applicant->points;
        }
      }

      //Additional Bonus for applicants
      if(config('kitamatch_config.additionalCriteriaBonus_12')){
        if ($applicant->additionalCriteria_12 == $provider_id) {
          $applicant->points = $applicant->points + config('kitamatch_config.additionalCriteriaBonus_12_value');
        } else {
          $applicant->points = $applicant->points;
        }
      }

      //} else {
        //no guardian -> order = 10000, to order asc
      //  $applicant->order = 0;
      //}
      //highly important applicants
      if ($applicant->status == 25) {
        $applicant->order = 2 * 12;
      }
    }

    // 2. sort: i) by manual points in the db, ii) by order tag
    // [tie braker, sort by birthday on the same level
    // https://github.com/laravel/ideas/issues/11;]
    if (config('kitamatch_config.manual_points')) { // order by manual points
      // points_manual
      $applicants = $applicants->sort(function($a, $b) {
        if($a->points === $b->points) {
          if($a->birthday === $b->birthday) {
            return 0;
           }
          return $a->birthday < $b->birthday ? -1 : 1; //now, the older will get priority , if we switch +,- then younger will get priority
        }
        return $a->points < $b->points ? +1 : -1;
      });
    } else {
      // order
      $applicants = $applicants->sort(function($a, $b) {
        if($a->order === $b->order) {
          if($a->birthday === $b->birthday) {
            return 0;
           }
          return $a->birthday < $b->birthday ? 1 : -1; //now, the older will get priority , if we switch +,- then younger will get priority
        }
        return $a->order < $b->order ? -1 : +1;
      });
    }

    return $applicants;
  }

  /**
  * Get the lowest preference rank of an applicant.
  * For example applicant 1 has ranked (1,2,3,4), the lowest rank would be 4.
  *
  * @param integer $aid Applicant-ID
  * @return integer
  */
  public function getLowestRankApplicant($aid) {
    $sql = "SELECT rank FROM preferences WHERE id_from = " . $aid . " AND (pr_kind = 1) ORDER BY rank DESC LIMIT 1";
    $lowestRank = DB::select($sql);
    if (count($lowestRank) > 0) {
      $rank = $lowestRank['0']->rank;
    } else {
      $rank = 1;
    }
    return $rank;
  }

  /**
  * Get the lowest preference rank of a program.
  *
  * @param integer $pid Program-ID
  * @return integer
  */
  public function getLowestRankUncoordinatedProgram($pid) {
    $sql = "SELECT rank FROM preferences WHERE id_from = " . $pid . " AND (pr_kind = 3) ORDER BY rank DESC LIMIT 1";
    $lowestRank = DB::select($sql);
    if (count($lowestRank) > 0) {
      $rank = $lowestRank['0']->rank;
    } else {
      $rank = 1;
    }
    return $rank;
  }

  public function getAllDeletedCoordinatedPreferences($pid) {
    $prefs = DB::table('preferences')
      ->where('id_from', '=', $pid)
      ->where('status', '=', -2)
      ->where('pr_kind', '=', 2)
      ->get();
    return $prefs;
  }

  public function deleteAllActivePreferences($pid, $coordination) {
    if ($coordination == 1) {
      $pr_kind = 2;
    } else {
      $pr_kind = 3;
    }

    $pref = DB::table('preferences')
      ->where('id_from', '=', $pid)
      ->where('pr_kind', '=', $pr_kind)
      ->delete();
  }

  public $primaryKey = 'prid';
}
