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
 | Preference Controller
 |--------------------------------------------------------------------------
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PreferenceRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Preference;
use App\Program;
use App\Provider;
use App\Matching;
use App\Applicant;
use App\Capacity;
use App\Traits\GetPreferences;

/**
* This controller handles the preferences of applicants & programs: add/edit, arrange preferences, distinguish betwenn coordinated & uncoordinated programs.
*/
class PreferenceController extends Controller
{
  //include the trait 'GetPreferences'
  use GetPreferences;

  /**
  * Create a new controller instance, handle authentication
  *
  * @return void
  */
  public function __construct() {
        $this->middleware('auth');
  }

  public function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
            }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}

  /**
  * Store a single preference
  *
  * @param Illuminate\Http\Request $request request
  * @return App\Preference
  */
  public function store(Request $request) {
    $preference = new Preference;
    $preference->id_from = $request->from;
    $preference->id_to = $request->to;
    $preference->pr_kind = $request->pr_kind;
    //if no pr_kind, than it is the preference from an applicant
    if (!$request->pr_kind) {
      $preference->pr_kind = 0;
    }
    $preference->rank = $request->rank;
    $preference->status = $request->status;
    $preference->isValid = 0;
    $preference->invalidReason = "";
    $preference->provider_id = $request->provider_id;
    $preference->program_id = $request->program_id;
    $preference->save();

    //set active, if pr_kind = 3 & program is status = 13
    if ($preference->pr_kind == 3) {
      $program = Program::find($preference->id_from);
      if ($program->status == 13) {
        $program->update(array('status' => '12'));
      }
    }
    return $preference;
  }

  /**
  * Update a single preference
  *
  * @param Illuminate\Http\Request $request request
  * @return App\Preference
  */
  public function update(Request $request) {
    $preference = Preference::findOrFail($request->prid);
    $preference->id_from = $request->from;
    $preference->id_to = $request->to;
    $preference->pr_kind = $request->pr_kind;
    //if no pr_kind, than it is preference by applicant
    if (!$request->pr_kind) {
      $preference->pr_kind = 0;
    }
    $preference->rank = $request->rank;
    $preference->status = $request->status;
    $preference->isValid = 0;
    $preference->invalidReason = "";
    $preference->provider_id = $request->provider_id;
    $preference->program_id = $request->program_id;
    $preference->save();
    return $preference;
  }


    /**
    * Show a single preference in a view
    *
    * @param integer $prid Preference-ID
    * @return view preference.edit
    */
    public function show($prid) {
      $preference = Preference::find($prid);
      return view('preference.show', array('preference' => $preference));
    }

    /**
    * Show a listed preferences
    *
    * @return view preference.all
    */
    public function all() {
      $preferences = Preference::all();
      return view('preference.all', array('preferences' => $preferences));
    }

    // only admins should be able to call this function
    public function setPreferences() {
      $preferences = Preference::where('pr_kind', '=', 1); // check if there was already a set, if so: print an error and exit
      if ($preferences->count() > 0) {
        abort(403, 'Die Präferenzen wurden schon einmal gesetzt. Bitte kontaktieren Sie den Systemadministrator.');
      }

      // applicants
      $applicantModel = new Applicant;
      $applicants = $applicantModel->getAll();
      foreach ($applicants as $applicant) {
        $this->setPreferencesByApplicant($applicant->aid);
      }

      // coordinated programs
      $this->createCoordinatedPreferences();

      return redirect()->action('AdminController@index');
    }

  // -----------------------------------------------------------------------------------------------
  // -----------------------------------------------------------------------------------------------
  // -----------------------------------------------------------------------------------------------
  // Applicants
  // -----------------------------------------------------------------------------------------------

  /**
  * Show the feasible set (pr_kind == 0) of an applicant on a view
  *
  * @param integer $aid Applicant-ID
  * @return view preference.showByApplicant
  */
  public function showByApplicant($aid) {
    $Applicant = new Applicant;
    $Program = new Program;
    $Provider = new Provider;
    $applicant = $Applicant::find($aid);
    $preferences = $this->getPreferencesByApplicant($aid);

    $programs = $Program->getAll()->where('age_cohort', '=', $applicant->age_cohort);
    $providers = $Provider::all();
    foreach ($preferences as $preference) {
      $program = $programs->find($preference->id_to);
      $provider = $providers->find($program->proid);
      $preference->programName = $provider->name . " - " . $program->name;
    }
    $select = array();
    $select[-1] = "Bitte auswählen...";
    foreach ($programs as $program) {
      if (!($preferences->contains('id_to', $program->pid))) {
        $provider = $providers->find($program->proid);
        $select[$program->pid] = $provider->name . " - " . $program->name;
      }
    }
    asort($select);

    return array('preferences' => $preferences, 'programs' => $select);
  }

  /**
  * Add a institution ot the feasible set (pr_kind == 0) of an applicant
  *
  * @param Illuminate\Http\Request $request request
  * @param integer $aid Applicant-ID
  * @return action PreferenceController@showByApplicant
  */
  public function addByApplicant(Request $request, $aid) {
    if ($request->id_to != -1) { // should be a real program
      $Preference = new Preference;
      $rank = $Preference->getLowestRankApplicant($aid) + 1;
      $preference = new Preference;
      $preference->id_from = $aid;
      $preference->id_to = $request->to;
      $preference->pr_kind = 0;
      $preference->rank = $rank;
      $preference->status = 1;
      $preference->isValid = 0;
      $preference->invalidReason = "";
      $preference->provider_id = $request->provider_id;
      $preference->program_id = $request->program_id;
      $preference->save();
    }
    return redirect()->action('ApplicantController@show', $aid);
  }

  /**
  * Change the order of the feasible of a single applicant, ajax sided
  *
  * @param App\Http\Requests $request request
  * @param integer $aid Applicant-ID
  * @return json
  */
  public function reorderByApplicantAjax(Request $request, $aid) {
    $programIds = $request->all();
    //https://laracasts.com/discuss/channels/laravel/sortable-list-with-change-in-database
    parse_str($request->order, $programs);
    foreach ($programs['item'] as $index => $preferenceId) {
      $preference = Preference::find($preferenceId);
      $preference->rank = $index+1;
      $preference->save();
    }
    return response()->json([
      'success' => true
    ]);
  }

  /**
  * Prepare the ajay sided deletion of a single element in the feasible set by an applicant
  *
  * @param App\Http\Requests $request request
  * @param integer $aid Applicant-ID
  * @return json
  */
  public function deleteByApplicantAjax(Request $request, $aid) {
    $prid = substr($request->itemId, strpos($request->itemId, "-") + 1);
    $this->deleteByApplicant($request, $prid);
    return response()->json([
      'success' => true
    ]);
  }

  /**
  * Delete a single element in the feasible set by an applicant
  *
  * @param App\Http\Requests $request request
  * @param integer $prid Preference-ID
  * @return App\Preference
  */
  public function deleteByApplicant(Request $request, $prid) {
    $preference = Preference::find($prid);
    //temp: set status=0 instead of deleting
    $preference->status = 0;
    $preference->save();
    return $preference;
  }

  // write applicant's preferences based on the feasible set, by care start & scope
  public function setPreferencesByApplicant($aid) {
    $applicant = Applicant::find($aid);
    $feasible_set = Preference::where('pr_kind', '=', 0)->where('id_from', '=', $aid)->where('status', '=', 1)->orderBy('rank')->get();

    $preference_list = array();

    foreach($feasible_set as $key => $preference) {
      $pid = $preference->id_to;
      $rank = $preference->rank;
      $provider_id = $preference->provider_id;
      $program_id = $preference->program_id;
      foreach (config('kitamatch_config.care_scopes') as $key_scope => $care_scope) {
        foreach (config('kitamatch_config.care_starts') as $key_start => $care_start) {
          if ($key_start >= $applicant->care_start and ($key_scope != -1 and $key_start != -1)) {
            $id_to = $pid . '_' . $key_start . '_' . $key_scope;

            //at the moment just two scopes
            $scope_rank = ($applicant->care_scope == $key_scope)? 1 : 2;
            $scope_is_first =($applicant->care_scope == $key_scope)? 1 : 0;

            $preference_list[] = array(
              'pid' => $pid,
              'start' => $key_start,
              'scope' => $key_scope,
              'program_rank' => $rank,
              'id_to' => $id_to,
              'scope_is_first' => $scope_is_first,
              'scope_rank' => $scope_rank,
              'provider_id' => $provider_id,
              'program_id' => $program_id,
            );
          }
        }
      }
    }

    if ($applicant->alternative_scope == 1 and $applicant->alternative_start == 1) {
      // both: yes
      $sorted = $this->array_orderby(
        $preference_list,
        'scope_rank', SORT_ASC, //scope is first priority, then ->Start and last ->program
        'start', SORT_ASC,
        'program_rank', SORT_ASC
      );

    } elseif ($applicant->alternative_scope == 1 and $applicant->alternative_start == 0) {
      // alternative_scope: yes, alternative_start: no
      /*$care_start = $applicant->care_start;
      $filtered = array_filter(
        $preference_list,
        function ($var) use ($care_start)  {
          return ($var['start'] == $care_start);
        }
      );

      $sorted = $this->array_orderby(
        $filtered,
        'scope_rank', SORT_ASC,
        'program_rank', SORT_ASC
      );*/

      $sorted = $this->array_orderby(
        $preference_list,
        'scope_rank', SORT_ASC,
        'start', SORT_ASC,
        'program_rank', SORT_ASC
      );

    } elseif ($applicant->alternative_scope == 0 and $applicant->alternative_start == 1) {
      // alternative_scope: no, alternative_start: yes
      $filtered = array_filter(
        $preference_list,
        function ($var) {
          return ($var['scope_is_first'] == 1);
        }
      );

      $sorted = $this->array_orderby(
        $filtered,
        'start', SORT_ASC,
        'program_rank', SORT_ASC 
      );

    } elseif ($applicant->alternative_scope == 0 and $applicant->alternative_start == 0) {
      // alternative_scope: no, alternative_start: no

      $care_start = $applicant->care_start;
      $filtered = array_filter(
        $preference_list,
        function ($var) {
          return ($var['scope_is_first'] == 1);
        }
      );
      /*$filtered = array_filter(
        $filtered,
        function ($var) use ($care_start) {
          return ($var['start'] == $care_start);
        }
      );*/

      $sorted = $this->array_orderby(
        $filtered,
        'start', SORT_ASC, // the earlier the better
        'program_rank', SORT_ASC
      );

    } else {
      // default
      $sorted = $this->array_orderby(
        $preference_list,
        'scope_rank', SORT_ASC,
        'start', SORT_ASC, // the earlier the better
        'program_rank', SORT_ASC
      );
    }

    $i = 1;
    foreach($sorted as $preference) {
      $request = new Request();
      $request->setMethod('POST');

      $rank = $i;

      $request->request->add([
        'from' => $applicant->aid,
        'to' => $preference['id_to'],
        'pr_kind' => 1,
        'status' => 1,
        'rank' => $rank,
        'provider_id' => $preference['provider_id'],
        'program_id' => $preference['program_id']
      ]);

      $this->store($request);

      $i = $i + 1;
    }
  }

  public function isSet() {
    $preferences = Preference::where('pr_kind', '=', 1)->get();
    if ($preferences->count() > 0) {
      return True;
    } else {
      return False;
    }
  }

  // -----------------------------------------------------------------------------------------------
  // -----------------------------------------------------------------------------------------------
  // Programs
  // -----------------------------------------------------------------------------------------------

  /**
  * Show all preferences of a program on a view
  *
  * @param integer $pid Program-ID
  * @return view preference.showByProgram
  */
  public function showByProgram($pid) {
    //check if coordinated or not
    $program = Program::find($pid);
    if ($program->coordination == 1) {
      //coordination: true
      $Matching = new Matching();
      $Preference = new Preference();

      $matches = $Matching->getMatchesByProgram($program->pid);
      $preferences = $this->getPreferencesByProgram($pid);
      $deletedPreferences = $Preference->getAllDeletedCoordinatedPreferences($pid);
      $program->currentOffers = 0;

      foreach ($preferences as $preference) {
        $preference->openOffer = 0;
        $preference->finalMatch = 0;

        $applicant = Applicant::find($preference->id_to);

        if ($applicant->status == 26 AND $matches->contains('aid', $applicant->aid)) {
          $preference->finalMatch = 1;
          $program->currentOffers = $program->currentOffers + 1;
        } elseif ($matches->where('status', '=', 31)->contains('aid', $applicant->aid)) {
          $preference->openOffer = 1;
          $program->currentOffers = $program->currentOffers + 1;
        }

        $preference->applicantLastName = $applicant->last_name;
        $preference->applicantFirstName = $applicant->first_name;
        $preference->applicantBirthday = $applicant->birthday;
        $preference->applicantGender = $applicant->gender;
      }

      //for all deleted prefs
      foreach ($deletedPreferences as $preference) {
        $applicant = Applicant::find($preference->id_to);
        $preference->applicantLastName = $applicant->last_name;
        $preference->applicantFirstName = $applicant->first_name;
        $preference->applicantBirthday = $applicant->birthday;
        $preference->applicantGender = $applicant->gender;
      }

      return view('preference.showByProgram', array('preferences' => $preferences,
                                                    'program' => $program,
                                                    'matches' => $matches,
                                                    'deletedPreferences' => $deletedPreferences));
    } else {
      $program = Program::find($pid);
      //coordination: false
      $Program = new Program();
      $Matching = new Matching();
      $Provider = new Provider();
      $Matching = new Matching();
      $Preference = new Preference();
      $Capacity = new Capacity();

      $round = $Matching->getRound(); //current vs. past 
      $lastMatch = $Matching->lastMatch();

      $providerId = $Program->getProviderId($pid);
      if ($providerId) {
        $provider = true;
        $program->provider_name = Provider::find($providerId)->name;
      } else {
        $provider = false;
      }

      $capacities = app('App\Http\Controllers\CapacityController')->getProgramCapacities($pid);

      $availableApplicants = $Preference->getAvailableApplicants($pid);
      // order applicants
      $availableApplicants = $Preference->orderByCriteria($availableApplicants, $providerId, $provider);

      $preferences = $this->getPreferencesUncoordinatedByProgramCollection($pid); //!!

      //manual ranking
      $manualRanking = $this->getManualRankingsByProgram($pid);
      if (count($manualRanking) > 0) {
        //sort $availableApplicants by preference rank (status = -3)
        foreach($manualRanking as $rank_pref) {
          $applicant = $availableApplicants->where('aid', '=', $rank_pref->id_to)->first();
          $applicant->manualRank = $rank_pref->rank;
        }

        $availableApplicants = $availableApplicants->sortBy('manualRank');
      }
      //---

      //create empty array for services
      $offers = array();
      $openOffers = array();
      foreach (config('kitamatch_config.care_starts') as $key_start => $start) {
        foreach (config('kitamatch_config.care_scopes') as $key_scope => $scope) {
          if ($key_start != -1 && $key_scope != -1) {
            $openOffers[$key_start][$key_scope] = 0;
          }
        }
      }
      $countWaitlist = $openOffers; // 0 init
      $countApplicants = $openOffers; // 0 init
      //---

      //services
      $servicesApplicants = array();
      foreach ($availableApplicants as $applicant) {
        $servicesApplicants[$applicant->aid] = $this->getServicesByApplicantProgram($applicant->aid, $program->pid);
        foreach ($servicesApplicants[$applicant->aid] as $key_start => $level_start) {
          foreach ($level_start as $key_scope => $level_scope) {
            if ($scope) {
              $countApplicants[$key_start][$key_scope]++;
            }
          }
        }
      }
      //---

      //preferences
      foreach ($preferences as $preference) {
        foreach ($availableApplicants as $applicant) {
          if ($preference->id_to == $applicant->aid) {
            if ($preference->status == 1) {
              $id_from_split = explode("_", $preference->id_from);
              $pid = $id_from_split[0];
              $start = $id_from_split[1];
              $scope = $id_from_split[2];

              $offers[$applicant->aid]['id'] = $preference->prid;
              $offers[$applicant->aid]['rank'] = $preference->rank;
              $offers[$applicant->aid]['id_to'] = $preference->id_to;
              $offers[$applicant->aid]['id_from'] = $preference->id_from;
              $offers[$applicant->aid]['pid'] = $pid;
              $offers[$applicant->aid]['start'] = $start;
              $offers[$applicant->aid]['scope'] = $scope;
              $offers[$applicant->aid]['status'] = $preference->status;
              $offers[$applicant->aid]['updated_at'] = $preference->updated_at;
              if ($applicant->status == 26) {
                $offers[$applicant->aid]['final'] = 1;
              } else {
                $offers[$applicant->aid]['final'] = 0;
              }

              if ($preference->rank == 1) {
                $openOffers[$start][$scope] = $openOffers[$start][$scope] + 1;
              } else {
                $countWaitlist[$start][$scope] = $countWaitlist[$start][$scope] + 1;
              }

            } else if ($preference->status == -1) {
              // not successfull
              $offers[$applicant->aid]['id'] = $preference->prid;
              $offers[$applicant->aid]['final'] = -1;
              $offers[$applicant->aid]['status'] = -1;

              $offers[$applicant->aid]['rank'] = $preference->rank;
              $offers[$applicant->aid]['id_to'] = $preference->id_to;
              $offers[$applicant->aid]['id_from'] = $preference->id_from;
              $offers[$applicant->aid]['pid'] = $pid;
              $offers[$applicant->aid]['start'] = $start;
              $offers[$applicant->aid]['scope'] = $scope;
              $offers[$applicant->aid]['status'] = $preference->status;
              $offers[$applicant->aid]['updated_at'] = $preference->updated_at;
            }
          }
        }
      }
          
      foreach($availableApplicants as $applicant){
        $appliacntPreferences = $Preference->getPreferencesByApplicant($applicant->aid, $pid);
       // $applicant->rejectedBestOffer = 0;

        //available offer check
        if(count($appliacntPreferences) > 0){
          foreach($appliacntPreferences as $preference){
            $id_to_split = explode("_", $preference->id_to);
            $pid = $id_to_split[0];
            $start = $id_to_split[1];
            $scope = $id_to_split[2];

            $scopeCapacity = $Capacity->getScopeCapacity($pid, $start, $scope);
            $openOffer = $Preference->getCurrentOfferOfScope($preference->id_to);
            
            if($scopeCapacity != 0 && $scopeCapacity > count($openOffer)){
              $offeredPreference = $Preference->getOfferedPreference($preference->id_to, $applicant->aid);
              if( count($offeredPreference) > 0 && $offeredPreference[0]->status == '-1') {
                Preference::where('id_from','=',$applicant->aid)->where('id_to','=',$preference->id_to)->update(array('isValid'=>'0', 'invalidReason'=>'Absage'));
              }else{
                Preference::where('id_from','=',$applicant->aid)->where('id_to','=',$preference->id_to)->update(array('isValid'=>'1'));                  
              }

              // if(( $applicant->care_start == $start && $applicant->care_scope == $scope ) && ($offeredPreference[0]->status == '-1')){
              //   $applicant->rejectedBestOffer = 1;            
              // }
      
            }else{
             Preference::where('id_from','=',$applicant->aid)->where('id_to','=',$preference->id_to)->update(array('isValid'=>'0', 'invalidReason'=>'keine Kapazität'));
            }

          }
        }
        $appliacntPreferencesUpdated = $Preference->getPreferencesByApplicant($applicant->aid, $pid);
        
        if(count($appliacntPreferencesUpdated->where('isValid', '=', '1'))>0){
          $applicant->offerStatus = 1;
        }else{
          $applicant->offerStatus = 0;
        }

        $applicant->siblingsIsPresent = ($applicant->siblings == $providerId ? "Ja" : "Nein");

        if( !empty($applicant->sibling_applicant_id1)){
          $sibling_preference = $Preference->getAllPreferencesByApplicantID($applicant->sibling_applicant_id1);
          foreach($sibling_preference as $preference){
            if($preference->provider_id == $providerId ){
              $applicant->sibling_applicant_id_1 = $applicant->sibling_applicant_id1;
            }
          }
        }

        if( !empty($applicant->sibling_applicant_id2)){
          $sibling_preference = $Preference->getAllPreferencesByApplicantID($applicant->sibling_applicant_id2);
          foreach($sibling_preference as $preference){
            if($preference->provider_id == $providerId ){
              $applicant->sibling_applicant_id_2 = $applicant->sibling_applicant_id2;
            }
          }
        }

        if( !empty($applicant->sibling_applicant_id3)){
          $sibling_preference = $Preference->getAllPreferencesByApplicantID($applicant->sibling_applicant_id3);
          foreach($sibling_preference as $preference){
            if($preference->provider_id == $providerId ){
              $applicant->sibling_applicant_id_3 = $applicant->sibling_applicant_id3;
            }
          }
        }

      }
      
      $program->openOffers = $openOffers;

      return view('preference.uncoordinated', array('round' => $round,
                                                    'program' => $program,
                                                    'lastMatch' => $lastMatch,
                                                    'availableApplicants' => $availableApplicants,
                                                    'preferences' => $preferences,
                                                    'offers' => $offers,
                                                    'capacities' => $capacities,
                                                    'countApplicants' => $countApplicants,
                                                    'servicesApplicants' => $servicesApplicants,
                                                    'manualRanking' => $manualRanking,
                                                    'preferences' => $preferences)
                  );
    }
  }


  /**
  * Add a preference by program
  *
  * @param App\Http\Requests $request request
  * @param integer $pid Program-ID
  * @return action PreferenceController@showByProgram
  */
  public function addByProgram(Request $request, $pid) {
    $preference = new Preference;
    $preference->id_from = $pid;
    $preference->id_to = $request->to;
    $preference->pr_kind = 2;
    $preference->rank = $request->rank;
    $preference->status = 1;
    $preference->isValid = 0;
    $preference->invalidReason = "";
    $preference->provider_id = $request->provider_id;
    $preference->program_id = $request->program_id;
    $preference->save();
    return redirect()->action('PreferenceController@showByProgram', $pid);
  }

  public function undoByProgram(Request $request, $pid) {
    $preference = Preference::findOrFail($request->prid);
    $preference->status = 1;
    $preference->save();
    return redirect()->action('PreferenceController@showByProgram', $pid);
  }

  /**
  * Update a preference status to 0 by program
  *
  * @param App\Http\Requests $request request
  * @param integer $prid Prefernce-ID
  * @return action PreferenceController@showByProgram
  */
  public function deleteByProgram(Request $request, $prid) {
    $preference = Preference::find($prid);
    $pid = explode("_", $preference->id_from)[0];
    $preference->status = -2;
    $preference->save();
    return redirect()->action('PreferenceController@showByProgram', $pid);
  }

  public function deleteMultipleByProgram(Request $request) {
    foreach($request->deleteRows as $prid) {
      $preference = Preference::find($prid);
      $preference->status = -2;
      $preference->save();
    }
    return redirect()->action('PreferenceController@showByProgram', $preference->id_from);
  }

  /**
  * Add an offer preference by uncoordinated program
  *
  * @param App\Http\Requests $request request
  * @param integer $pid Program-ID
  * @return action PreferenceController@showByProgram
  */
  public function addOfferUncoordinatedProgram(Request $request, $pid) {
    $preference = new Preference;
    $existing_preference = $preference->getPreferenceByApplicantAndSid($request->aid, $request->sid);
   
    $preference->id_from = $request->sid; // service id
    $preference->id_to = $request->aid;
    $preference->pr_kind = 3;
  //  $preference->rank = $existing_preference[0]->rank;
    $preference->rank = 1;
    $preference->status = 1;
    $preference->isValid = 0;
    $preference->invalidReason = "";
    $preference->provider_id = $existing_preference[0]->provider_id;
    $preference->program_id = $existing_preference[0]->program_id;
    $preference->save();

    return redirect()->action('PreferenceController@showByProgram', $pid);
  }

  /**
  * Change the preference order in a waitlist by an uncoordinated program, ajax sided
  *
  * @param App\Http\Requests $request request
  * @param integer $pid Program-ID
  * @return json
  */
  public function reorderWaitlistByProgramAjax(Request $request, $pid) {
    $applicantIds = $request->all();
    //https://laracasts.com/discuss/channels/laravel/sortable-list-with-change-in-database
    parse_str($request->order, $applicants);
    foreach ($applicants['item'] as $index => $preferenceId) {
      $preference = Preference::find($preferenceId);
      //waitlist prefs start with rank >= 2 and not 0
      $preference->rank = $index + 2;
      $preference->save();
    }
    return response()->json([
      'success' => $applicants['item']
    ]);
  }

  public function reorderByProgramAjax(Request $request, $pid) {
    $preferenceIds = $request->all();
    parse_str($request->order, $preferences);
    foreach ($preferences['item'] as $index => $preferenceId) {
      $preference = Preference::find($preferenceId);
      //waitlist prefs start with rank >= 2 and not 0
      $preference->rank = $index + 2;
      $preference->save();
    }
    return response()->json([
      'success' => 1
    ]);
  }

  /**
  * Update an waitinglist preference to an definite offer by uncoordinated program
  *
  * @param App\Http\Requests $request request
  * @param integer $prid Preference-ID
  * @return action PreferenceController@showByProgram
  */
  public function updateOfferUncoordinatedProgram(Request $request) {
    $preference = Preference::find($request->prid);
    $preference->rank = 1;
    $preference->save();

    return redirect()->action('PreferenceController@showByProgram', $preference->id_from);
  }

  /**
  * Add a waitlist preference by uncoordinated program
  *
  * @param App\Http\Requests $request request
  * @param integer $pid Program-ID
  * @return action PreferenceController@showByProgram
  */
  public function addWaitlistUncoordinatedProgram(Request $request, $pid) {
    $preference = new Preference;
    $lowestRank = $preference->getLowestRankUncoordinatedProgram($pid);

    $preference->id_from = $pid;
    $preference->id_to = $request->aid;
    $preference->pr_kind = 3;
    if ($lowestRank > 1) {
      $preference->rank = $lowestRank + 1;
    } else {
      $preference->rank = 2;
    }
    $preference->status = 1;
    $preference->isValid = 0;
    $preference->invalidReason = "";
    $preference->provider_id = $request->provider_id;
    $preference->program_id = $request->program_id;
    $preference->save();

    return redirect()->action('PreferenceController@showByProgram', $pid);
  }

  // ------------------------------------------------------------------------------------
  // ------------------------------------------------------------------------------------
  // ------------------------------------------------------------------------------------


  /**
  * Create the preferences of all coordinated programs by their corresponding criteria catalogues.
  *
  * @return void
  */
  public function createCoordinatedPreferences() {
    $Program = new Program;
    //get all programs with coordination = true
    $programs = $Program->getCoordinated();
    foreach ($programs as $program) {
      $this->createCoordinatedPreferencesByProgram($program);
    }
  }

  public function createCoordinatedPreferencesByProgram($program) {
    $Program = new Program;
    $Preference = new Preference;
    $Applicant = new Applicant;
    //not all but only the available ones
    $applicants = $Preference->getAvailableApplicants($program->pid);

    //als eigene funktion bauen & die drüber nur diese aufrufen lassen
    $providerId = $Program->getProviderId($program->pid);
    if ($providerId) {
      $provider = true;
      $p_id = $program->proid;
    } else {
      $provider = false;
      $p_id = $program->pid;
    }

    $applicantsByProgram = $Preference->orderByCriteria($applicants, $p_id, $provider);

    $rank = 1;
    foreach (config('kitamatch_config.care_scopes') as $key_scope => $care_scope) {

      foreach (config('kitamatch_config.care_starts') as $key_start => $care_start) {

        if ($key_start >= $applicant->care_start and ($key_scope != -1 and $key_start != -1)) {
          $id_to = $pid . '_' . $key_start . '_' . $key_scope;

    foreach ($applicantsByProgram as $applicant) {
      //look if preference exists and if it has to be updated
      $preference = Preference::where('id_from', '=', $program->pid . '_' . $key_start . '_' . $key_scope)
        ->where('id_to', '=', $applicant->aid)
        ->where('pr_kind', '=', 2)
        ->where('status', '=', 1)->first();

      //construct (update) new preference
      $request = new Request();
      $request->setMethod('POST');
      $request->request->add(['from' => $program->pid . '_' . $key_start . '_' . $key_scope,
                              'to' => $applicant->aid,
                              'pr_kind' => 2,
                              'rank' => $rank,
                              'status' => 1
                            ]);

      //does a preference exist?
      if ($preference != null) {
        //update
        $request->request->add(['prid' => $preference->prid]);
        $this->update($request);
      } else {
        //generate preference
        $this->store($request);
      }
      $rank = $rank + 1;
    }
  }

}}

    }


  public function rebuildCoordinatedProgramPreferences($pid) {
    $Preference = new Preference();
    $program = Program::find($pid);
    $Preference->deleteAllActivePreferences($pid, 1);
    $this->createCoordinatedPreferencesByProgram($program);

    return redirect()->action('PreferenceController@showByProgram', $pid);
  }

}
