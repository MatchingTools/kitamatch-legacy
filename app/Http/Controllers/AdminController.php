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
 | Admin Controller
 |--------------------------------------------------------------------------
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use App\Applicant;
use App\Matching;
use App\Provider;
use App\Program;
use App\Code;

/**
* This controller handles the administration side. It creates the admin dashboard and routes to various tasks.
*/
class AdminController extends Controller
{
  public function __construct(){
      $this->middleware('auth');
  }

  public function index() {
    $matches = $this->listMatchings();
    $data = $this->generateDashboard();

    return view('admin.dashboard', array('matches' => $matches, 'data' => $data));
  }

  public function listMatchings() {
    // Eager load related models
    $matches = Matching::whereIn('status', [31, 32])
        ->with(['applicant', 'program.provider', 'statusCode'])
        ->get();
    
    $scopes = config('kitamatch_config.care_scopes');
    $starts = config('kitamatch_config.care_starts');

    foreach ($matches as $match) {
      $applicant = $match->applicant;
      $program = $match->program;
      $provider = $program->provider;

      $match->applicant_name = $applicant->first_name . " " . $applicant->last_name;
      $match->points_manual = $applicant->points_manual;
      $match->start_date = $applicant->start_date;
      $match->additional_criteria_1 = $applicant->additionalCriteria_1;
      $match->additional_criteria_2 = $applicant->additionalCriteria_2;
      $match->additional_criteria_3 = $applicant->additionalCriteria_3;
      $match->additional_criteria_4 = $applicant->additionalCriteria_4;
      $match->additional_criteria_5 = $applicant->additionalCriteria_5;
      $match->additional_criteria_6 = $applicant->additionalCriteria_6;
      $match->additional_criteria_7 = $applicant->additionalCriteria_7;
      $match->additional_criteria_8 = $applicant->additionalCriteria_8;
      $match->additional_criteria_9 = $applicant->additionalCriteria_9;
      $match->additional_criteria_10 = $applicant->additionalCriteria_10;
      $match->additional_criteria_11 = $applicant->additionalCriteria_11;
      $match->additional_criteria_12 = $applicant->additionalCriteria_12;
      $match->program_name = $program->name;
      $match->provider_name = $provider->name;
      $match->status_text = $match->statusCode->value;

      $preferences = DB::table('preferences')
            ->where('id_from', '=', $applicant->aid)
            ->where('pr_kind', '=', 0)
            ->orderBy('rank', 'asc')
            ->get();
     
      $preference_providers = [];
      foreach ($preferences as $preference) {
          $preference_provider = Provider::find($preference->provider_id);
          if ($preference_provider) {
              $preference_providers[] = $preference_provider->name;
          }    
      }

      for ($i = 1; $i <= 12; $i++) {
          $match->{"preference_$i"} = isset($preference_providers[$i - 1]) ? $preference_providers[$i - 1] : '';
      }

      $pid_split = explode("_", $match->pid);
      $match->start = $starts[$pid_split[1]];
      $match->scope = $scopes[$pid_split[2]];
    }
    return $matches;
  }

  public function exportMatching() {
    $matchings = $this->listMatchings();
    $filename = "matchings.csv";
    $handle = fopen('php://output', 'w');
    fputcsv($handle, array('Kita', 'Bewerber', 'Status'));
    foreach($matchings as $match) {
        //fputcsv($handle, array($match->program_name, $match->applicant_name, $match->status_text));
        fputcsv($handle, array(".."));
    }
    fclose($handle);
    $headers = array(
        'Content-Type' => 'text/csv',
    );
    Response::download($handle, $filename, $headers);
    return redirect()->action('AdminController@index');
  }

  public function generateDashboard() {
    $Matching = new Matching;

    $data = array();
    $age_cohorts = config('kitamatch_config.age_cohorts');
    $scopes = config('kitamatch_config.care_scopes');
    $starts = config('kitamatch_config.care_starts');

    $applicants = Applicant::with(['matches'])->get();
    $programs = Program::all();
    $providers = Provider::all();
    $matching = $Matching->getActiveMatches();
    $data['applicants'] = $applicants;
    $data['applicantsCount'] = $applicants->count();
    $data['applicantsVerified'] = count(Applicant::whereIn('status', [22, 25, 26])->get());
    $data['applicantsFinal'] = count(Applicant::where('status', '=', 26)->get());


    $nonMatches = array();
    foreach ($applicants as $applicant) {
      $filter = DB::table('matches')->where('aid', '=', $applicant->aid)->first();
      if (count($filter) == 0) {
        $nonMatches[$applicant->aid] = [
          'aid' => $applicant->aid,
          'first_name' => $applicant->first_name,
          'last_name' => $applicant->last_name,
          'birthday' => $applicant->birthday,
          'gender' => $applicant->gender,
          'age_cohort' => $age_cohorts[$applicant->age_cohort],
          'care_scope' => $scopes[$applicant->care_scope],
          'care_start' => $starts[$applicant->care_start],
          'points_manual' => $applicant->points_manual,
          'additional_criteria_1' => $applicant->additionalCriteria_1,
          'additional_criteria_2' => $applicant->additionalCriteria_2,
          'additional_criteria_3' => $applicant->additionalCriteria_3,
          'additional_criteria_4' => $applicant->additionalCriteria_4,
          'additional_criteria_5' => $applicant->additionalCriteria_5,
          'additional_criteria_6' => $applicant->additionalCriteria_6,
          'additional_criteria_7' => $applicant->additionalCriteria_7,
          'additional_criteria_8' => $applicant->additionalCriteria_8,
          'additional_criteria_9' => $applicant->additionalCriteria_9,
          'additional_criteria_10' => $applicant->additionalCriteria_10,
          'additional_criteria_11' => $applicant->additionalCriteria_11,
          'additional_criteria_12' => $applicant->additionalCriteria_12,
        ];

        $preferences = DB::table('preferences')
        ->where('id_from', '=', $applicant->aid)
        ->where('pr_kind', '=', 0)
        ->orderBy('rank', 'asc')
        ->get();

        $preference_providers = [];
        foreach ($preferences as $preference) {
            $preference_provider = Provider::find($preference->provider_id);
            if ($preference_provider) {
                $preference_providers[] = $preference_provider->name;
            }    
        }

        for ($i = 1; $i <= 12; $i++) {
          $nonMatches[$applicant->aid]["preference_$i"] = isset($preference_providers[$i - 1]) ? $preference_providers[$i - 1] : '';
        }
      }
    }
    $data['non-matches'] = $nonMatches;

    $data['isSet'] = app('App\Http\Controllers\PreferenceController')->isSet();

    $data['programsCount'] = count($programs);
    $data['providersCount'] = count($providers);
    $capacitySql = "SELECT SUM(capacity) AS 'totalCapacity' FROM capacities";
    $data['totalCapacity'] = DB::select($capacitySql)['0']->totalCapacity;
    $data['countRounds'] = $Matching->getRound();
    return $data;
  }

  public function resetDB() {
    //definition: 1) delete all matchings, 2) reset all applicant to status == 22, 3) delete all program preferences, 4) do not edit applicant preferences

    //TO-DO
    // manual order of applicants is also lost

    //1)
    DB::table('matches')->truncate();

    //2)
    DB::table('applicants')->update(['status' => 22]);

    //4)
    DB::table('preferences')->whereIn('pr_kind', [3])->whereIn('status', [-3, -2, -1, 0, 1])->delete();

    return redirect()->action('AdminController@index');
  }

  public function exportAssignedApplicants(){
    $matches = $this->listMatchings();
    $matches_array[] = array(
      'ID',
      'Bewerber',
      'Kita',
      'Kitagruppe',
      'Status',
      'Quartal',
      'Umfang',
      'Beginn',
      'Rangordnungspunkte',
      'Wunscheinrichtung',
      '2. Wunsch',
      '3. Wunsch',
      '4. Wunsch',
      '5. Wunsch',
      '6. Wunsch',
      '7. Wunsch',
      '8. Wunsch',
      '9. Wunsch',
      '10. Wunsch',
      '11. Wunsch',
      '12. Wunsch',
      'Zusatzkriterium1',
      'Zusatzkriterium2',
      'Zusatzkriterium3',
      'Zusatzkriterium4',
      'Zusatzkriterium5',
      'Zusatzkriterium6',
      'Zusatzkriterium7',
      'Zusatzkriterium8',
      'Zusatzkriterium9',
      'Zusatzkriterium10',
      'Zusatzkriterium11',
      'Zusatzkriterium12');

    $scopes = config('kitamatch_config.care_scopes');
    $starts = config('kitamatch_config.care_starts');

    foreach($matches as $match){
      
      $id_to_split = explode("_", $match->pid);
            $p_id = $id_to_split[0];
            $start = $starts[$id_to_split[1]];
            $scope = $scopes[$id_to_split[2]];

      $matches_array[] = array(
        'ID'=> $match->aid,
        'Bewerber'=> $match->applicant_name,
        'Kita' => $match->provider_name,
        'Kitagruppe' => $match->program_name,
        'Status' => $match->status_text,
        'Quartal' => $start,
        'Umfang' => $scope,
        'Beginn' => $match->start_date,
        'Rangordnungspunkte' => $match->points_manual,
        'Wunscheinrichtung' => $match->preference_1,
        '2. Wunsch' => $match->preference_2,
        '3. Wunsch' => $match->preference_3,
        '4. Wunsch' => $match->preference_4,
        '5. Wunsch' => $match->preference_5,
        '6. Wunsch' => $match->preference_6,
        '7. Wunsch' => $match->preference_7,
        '8. Wunsch' => $match->preference_8,
        '9. Wunsch' => $match->preference_9,
        '10. Wunsch' => $match->preference_10,
        '11. Wunsch' => $match->preference_11,
        '12. Wunsch' => $match->preference_12,
        'Zusatzkriterium1' => $this->getProviderName($match->additional_criteria_1),
        'Zusatzkriterium2' => $this->getProviderName($match->additional_criteria_2),
        'Zusatzkriterium3' => $this->getProviderName($match->additional_criteria_3),
        'Zusatzkriterium4' => $this->getProviderName($match->additional_criteria_4),
        'Zusatzkriterium5' => $this->getProviderName($match->additional_criteria_5),
        'Zusatzkriterium6' => $this->getProviderName($match->additional_criteria_6),
        'Zusatzkriterium7' => $this->getProviderName($match->additional_criteria_7),
        'Zusatzkriterium8' => $this->getProviderName($match->additional_criteria_8),
        'Zusatzkriterium9' => $this->getProviderName($match->additional_criteria_9),
        'Zusatzkriterium10' => $this->getProviderName($match->additional_criteria_10),
        'Zusatzkriterium11' => $this->getProviderName($match->additional_criteria_11),
        'Zusatzkriterium12' => $this->getProviderName($match->additional_criteria_12),
      );
    };
    Excel::create('Zuordnungen', function($excel) use($matches_array){
      $excel->setTitle('Zuordnungen');
      $excel->sheet('Zuordnungen', function($sheet) use ($matches_array){
        $sheet->fromArray($matches_array, null, 'A1', false, false);
      });
    })->download('xlsx');
  }

  public function exportUnassignedApplicants(){
    $data = $this->generateDashboard();
    $nonMatches_array[] = array(
      'ID',
      'Bewerber',
      'Geburtsdatum',
      'Kitagruppe',
      'Quartal',
      'Umfang',
      'Rangordnungspunkte',
      'Wunscheinrichtung',
      '2. Wunsch',
      '3. Wunsch',
      '4. Wunsch',
      '5. Wunsch',
      '6. Wunsch',
      '7. Wunsch',
      '8. Wunsch',
      '9. Wunsch',
      '10. Wunsch',
      '11. Wunsch',
      '12. Wunsch',
      'Zusatzkriterium1',
      'Zusatzkriterium2',
      'Zusatzkriterium3',
      'Zusatzkriterium4',
      'Zusatzkriterium5',
      'Zusatzkriterium6',
      'Zusatzkriterium7',
      'Zusatzkriterium8',
      'Zusatzkriterium9',
      'Zusatzkriterium10',
      'Zusatzkriterium11',
      'Zusatzkriterium12');

    foreach($data['non-matches'] as $nonMatch){
      
      $nonMatches_array[] = array(
        'ID'=> $nonMatch['aid'],
        'Bewerber' => $nonMatch['first_name'].' '.$nonMatch['last_name'],
        'Geburtsdatum' => $nonMatch['birthday']->format('d.m.Y'),
        'age_cohort' => $nonMatch['age_cohort'],
        'care_start' => $nonMatch['care_start'],
        'care_scope' => $nonMatch['care_scope'],
        'Rangordnungspunkte' => $nonMatch['points_manual'],
        'Wunscheinrichtung' => $nonMatch['preference_1'],
        '2. Wunsch' => $nonMatch['preference_2'],
        '3. Wunsch' => $nonMatch['preference_3'],
        '4. Wunsch' => $nonMatch['preference_4'],
        '5. Wunsch' => $nonMatch['preference_5'],
        '6. Wunsch' => $nonMatch['preference_6'],
        '7. Wunsch' => $nonMatch['preference_7'],
        '8. Wunsch' => $nonMatch['preference_8'],
        '9. Wunsch' => $nonMatch['preference_9'],
        '10. Wunsch' => $nonMatch['preference_10'],
        '11. Wunsch' => $nonMatch['preference_11'],
        '12. Wunsch' => $nonMatch['preference_12'],
        'Zusatzkriterium1' => $this->getProviderName($nonMatch['additional_criteria_1']),
        'Zusatzkriterium2' => $this->getProviderName($nonMatch['additional_criteria_2']),
        'Zusatzkriterium3' => $this->getProviderName($nonMatch['additional_criteria_3']),
        'Zusatzkriterium4' => $this->getProviderName($nonMatch['additional_criteria_4']),
        'Zusatzkriterium5' => $this->getProviderName($nonMatch['additional_criteria_5']),
        'Zusatzkriterium6' => $this->getProviderName($nonMatch['additional_criteria_6']),
        'Zusatzkriterium7' => $this->getProviderName($nonMatch['additional_criteria_7']),
        'Zusatzkriterium8' => $this->getProviderName($nonMatch['additional_criteria_8']),
        'Zusatzkriterium9' => $this->getProviderName($nonMatch['additional_criteria_9']),
        'Zusatzkriterium10' => $this->getProviderName($nonMatch['additional_criteria_10']),
        'Zusatzkriterium11' => $this->getProviderName($nonMatch['additional_criteria_11']),
        'Zusatzkriterium12' => $this->getProviderName($nonMatch['additional_criteria_12'])
      );
    };
    Excel::create('Nicht zugeordnete Bewerber', function($excel) use($nonMatches_array){
      $excel->setTitle('Nicht zugeordnete Bewerber');
      $excel->sheet('Nicht zugeordnete Bewerber', function($sheet) use ($nonMatches_array){
        $sheet->fromArray($nonMatches_array, null, 'A1', false, false);
      });
    })->download('xlsx');
  }

  public function getProviderName($providerId) {
    if ($providerId == '870') {
        return '';
    }

    $provider = Provider::find($providerId);
    return $provider ? $provider->name : '';
  }
}
