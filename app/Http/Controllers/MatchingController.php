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
 | Matching Controller
 |--------------------------------------------------------------------------
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\MatchRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProgramController;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Matching;
use App\Applicant;
use App\Program;
use App\Preference;
use App\Capacity;
use App\Traits\GetPreferences;
use App\Mail\ApplicantMatch;
use App\Mail\ProgramMatch;

/**
* This controller is responsible for the matching process: preperation, call and handling of the Matchingtools API.
*/
class MatchingController extends Controller
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

  /**
  * Store a single matching entry with the current match status
  *
  * @param App\Http\Requests\MatchRequest $request request
  * @return App\Matching
  */
  public function store(Request $request) {
    $match = new Matching;
    $match->aid = $request->student;
    $match->pid = $request->college;
    $match->status = $request->status;
    $match->save();
    return $match;
  }

  /**
  * List all matchings in a view
  *
  * @return view matching.all
  */
  public function all() {
    $matches = DB::table('matches')->whereIn('status', [31, 32])->get();
    foreach ($matches as $match) {
      $applicant = Applicant::where('aid', '=', $match->aid)->first();
      $match->applicant_name = $applicant->last_name . " " . $applicant->first_name;
      $program = Program::where('pid', '=', $match->pid)->first();
      $match->program_name = $program->name;
    }
    return view('matching.all', array('matches' => $matches));
  }

  /**
  * Main matching method. Calls the MatchingTools.com-API after prepareMatching(), handles the response and writes matchings to the database.
  * At the moment there is no handling for invalid feedback from the API-call like Error 500.
  *
  * @return view AdminController@index
  */
  public function findMatchings() {
    $Program = new Program;
    $Applicant = new Applicant;
    $Preference = new Preference;
    $Matching = new Matching;

    $input = $this->prepareMatching2();

    //null=4
    if (!(strlen(json_encode($input))>5)) {
      return redirect()->action('AdminController@index');
    }
    print_r(json_encode($input));
    echo "<br><br><br><br><br><br>";


    //GuzzleHttp\Client
    $client = new Client();
    try {
    $response = $client->post('https://api.matchingtools.org/hri/demo?optimum=college-optimal',
      [
        'auth' => [
          'mannheim', 'Exc3llence!'
        ],
        'body' =>
          json_encode($input),
        'headers' => ['Accept' => 'application/json']
      ]);

    } catch (\GuzzleHttp\Exception\ServerException $e) {
      echo 'Uh oh! ' . $e->getMessage();
      return;
    }

    $api_status = $response->getStatusCode();
    if ($api_status == 500) {
      print("Exit the function");
      exit();
    }

    //write the matches
    $result = json_decode($response->getBody(), true);
    $matchingResult = $result['hri_matching'];

    print("Results:");
    print_r($result);

    //temp: set active = 0 for all previous entries != final
    $Matching->resetMatches();

    $Preference->resetUncoordinatedOffers();

    foreach ($matchingResult as $match) {
      $college = $match['college'];
      $student = $match['student'];
      $matchRequest = new Request();
      $matchRequest->setMethod('POST');
      $matchRequest->request->add(['college' => $college,
                                   'student' => $student
                                 ]);

      //check if program is uncoordinated
      $coordination = $Program->isCoordinated($match['college']);
      // is uncoordianted

      print("---------- <br>");

      if ($coordination == 0) {
        $preferencesUncoordinated = $this->getPreferencesByUncoordinatedService($match['college']);

        foreach ($preferencesUncoordinated as $preference) {
          if ($preference->id_to == $match['student']) {
            $Preference->updateStatus($preference->prid, 1); 
            $Preference->updateRank($preference->prid, 1);
            
          }
        }
      }

      # LOOK at every applicant that is in the matchingResults
      # for every pref that is below this rank for this applicant, and not status -1 already, set status to -3
      # for every pref that is below this rank for this applicant, set status -1
      $preferences = $Preference->getAllPreferencesByApplicantID($student);
      $college_preference = $Preference->getPreferenceDetails($college, $student);

      foreach($preferences as $preference){
        if($college != $preference->id_to && $college_preference[0]->rank < $preference->rank && $preference->status != -1){
          $Preference->updateStatus($preference->prid, -1); 
        }
      }

      //check if it's the final match
      if ($match['college'] == $input['student_prefs'][$match['student']][0]) {
        $matchRequest->request->add(['status' => 32]);
        $this->store($matchRequest);
        //set applicant status to matched
        app('App\Http\Controllers\ApplicantController')->setFinalMatch($match['student']);

        //for the queues, update all uncoordinated prefs to -1
      //  $Preference->resetAllUncoordnatedQueuesByApplicant($student, $college);

      } else {
        $matchRequest->request->add(['status' => 31]);
        $this->store($matchRequest);
      }
    }
  }


  /**
  * Create the structure necessary for the API-Call of MatchingTools.com, for more see code and https://matchingtools.com/#operation/hri_demo
  *
  * @return array
  */
  public function prepareMatching() {
    //Format requirements: https://matchingtools.com/#operation/hri_demo
    $Preference = new Preference;
    $Applicant = new Applicant;
    $json = [];
    $preferencesApplicants = [];

    //create coordinated prefs -> done via generateCoordinated
    //old because it is done via program
    //app('App\Http\Controllers\PreferenceController')->createCoordinatedPreferences();

    //look for non active programs
    app('App\Http\Controllers\ProgramController')->setNonActive();

    //--------------------
    //by applicant
    $applicants = $Applicant->getAll();
    $programsC = DB::table('programs')
      //exclude status code 13: inactive for 7 days
      ->where('status', '=', 12)
      ->where('coordination', '=', 1)
      ->get();
    $programsU = DB::table('programs')
      ->where('status', '=', 12)
      ->where('coordination', '=', 0)
      ->get();

    foreach ($applicants as $applicant) {
      $preferencesByApplicant = $this->getPreferencesByApplicant($applicant->aid);
      $preferenceList = array();
      foreach ($preferencesByApplicant as $preference) {
        if ($programsC->contains('pid', $preference->id_to) OR
          $programsU->contains('pid', $preference->id_to)) {
          $preferenceList[] = (string)$preference->id_to;
        }
      }
      //check if there are any preferences
      if (count($preferenceList) > 0) {
        $preferencesApplicants[$applicant->aid] = $preferenceList;
      }
    }
    if (count($preferencesApplicants)>0) {
      $json["student_prefs"] = $preferencesApplicants;
    } else {
      //there are no valid students listed, so abort
      return;
    }

    //--------------------
    //capacity
    $capacityPreList = array();
    $Program = new program;
    //1: coordinated
    foreach ($programsC as $program) {
      if ($Preference->hasPreferencesByProgram($program->pid)) {
        $pid = (string)$program->pid;
        $capacity = app('App\Http\Controllers\ProgramController')->getCapacity($program->pid);
        if ($capacity > 0) {
          $capacityPreList[$pid] = $capacity;
        }
      }
    }
    //2: uncoordinated
    foreach ($programsU as $program) {
      if ($Preference->hasPreferencesByProgram($program->pid)) {
        $pid = (string)$program->pid;
        $capacity = app('App\Http\Controllers\ProgramController')->getCapacity($program->pid);
        if ($capacity > 0) {
          $capacityPreList[$pid] = $capacity;
        }
      }
    }

    print_r($capacityPreList);

    //--------------------
    //by program
    $preferencesPrograms = array();
    //1: only program that take part in the coordinated way
    foreach ($programsC as $program) {
      if (array_key_exists($program->pid, $capacityPreList)) {
        $preferencesByProgram = $this->getPreferencesByProgram($program->pid);
        $preferenceList = array();
        foreach ($preferencesByProgram as $preference) {
          if ( array_key_exists($preference->id_to, $preferencesApplicants) && in_array($program->pid, $preferencesApplicants[$preference->id_to]) && ($Applicant::find($preference->id_to)->status == 22 || $Applicant::find($preference->id_to)->status == 25) ) {
          //if ( applicant hat das program auch eingetragen & applicant status == 22 ) ) {
            $preferenceList[] = (string)$preference->id_to;
          }
        }
        //check if there are any preferences
        if (count($preferenceList) > 0) {
          $preferencesPrograms[$program->pid] = $preferenceList;
        }
      }
    }
    //2: add the programs that take the uncoordinated way
    foreach ($programsU as $program) {
      if (array_key_exists($program->pid, $capacityPreList)) {
        $preferencesByProgram = $this->getPreferencesUncoordinatedByProgram($program->pid);
        $preferenceList = array();
        foreach ($preferencesByProgram as $preference) {
          //list only active preferences
          if ($preference->status == 1 && ($Applicant::find($preference->id_to)->status == 22 || $Applicant::find($preference->id_to)->status == 25)) {
            $preferenceList[] = (string)$preference->id_to;
          }
        }
        //check if there are any preferences
        if (count($preferenceList) > 0) {
          $preferencesPrograms[$program->pid] = $preferenceList;
        }
      }
    }
    if (count($preferencesPrograms) > 0) {
      $json["college_prefs"] = $preferencesPrograms;
    } else {
      //there are no valid programs listed, so abort
      return;
    }

    //check capacity for non existing programs again
    $capacityList = array();
    //dd($preferencesPrograms);
    foreach ($capacityPreList as $programID => $programCapacity) {
      if (array_key_exists($programID, $preferencesPrograms)) {
        $capacityList[$programID] = $programCapacity;
      }
    }
    $json["college_capacity"] = $capacityList;

print("Capacity List:");
print_r($capacityList);
print("<br><br>");

    return ($json);
  }

  public function prepareMatching2() {
    $Preference = new Preference;
    $Capacity = new Capacity;
    $Applicant = new Applicant;
    $Matching = new Matching;
    $json = array();


    // Applicants ------------------
    $preferencesApplicants = array();
    $applicants = $Applicant->getAll();

    foreach ($applicants as $applicant) {
      $preferencesByApplicant = $this->getServicesByApplicant($applicant->aid);
      $preferenceList = array();
      foreach ($preferencesByApplicant as $preference) {
        $preferenceList[] = (string)$preference->id_to;
      }
      //check if there are any preferences
      if (count($preferenceList) > 0) {
        $preferencesApplicants[$applicant->aid] = $preferenceList;
      }
    }
    if (count($preferencesApplicants) > 0) {
      $json["student_prefs"] = $preferencesApplicants;
    }

    // Services ------------------
    $preferencesServices = array();
    $capacities = array();
    $services = DB::table('preferences')->select('id_from')
      ->whereIn('pr_kind', [2,3])
      ->where('status', '=', 1)
      ->distinct()
      ->get();
    $preferencesByServices = DB::table('preferences')->whereIn('pr_kind', [2,3])
      ->where('status', '=', 1)
      ->orderBy('rank', 'asc')
      ->get();

    foreach($services as $service) {
      $i = 0;
      $preferencesByService = $preferencesByServices->where('id_from', '=', $service->id_from);
      foreach ($preferencesByService as $pref) {
        if (count($applicants->where('aid', '=', $pref->id_to)) > 0) { //applicant with status 22/25
          $preferencesServices[$service->id_from][$i] = $pref->id_to;
          $i++;
        }
      }

      $capacities[$service->id_from] = $Capacity->getCapacity($service->id_from);
    }
    $json['college_prefs'] = $preferencesServices;
    $json['college_capacity'] = $capacities;

    // Last Matching ------------------
    /*$lastMatchDate = $Matching->lastMatch();
    $lastMatchTime = strtotime($lastMatchDate);
    $lastMatchTime = $lastMatchTime - (1 * 60); // minus 1 minute
    $lastMatchDate = date("Y-m-d H:i:s", $lastMatchTime);
    $matches = DB::table('matches')
      ->where('updated_at', '>=', $lastMatchDate)
      ->get();

    $matching = array();
    foreach($matches as $id => $match) {
      $matching[$match->mid]['student'] = $match->aid;
      $matching[$match->mid]['college'] = $match->pid;
    }
    $json['matching'] = $matching;*/

    // ----------

    // If no information, return NULL
    if ($preferencesByServices->count() == 0 || count($preferencesApplicants) == 0) {
      return;
    }

    return($json);
  }

}
