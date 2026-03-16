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
  /**
   * Create a new controller instance, handle authentication
   *
   * @return void
   */
  public function __construct()
  {
      $this->middleware('auth');
  }

  public function index() {
    $matches = $this->listMatchings();
    $data = $this->generateDashboard();

    return view('admin.dashboard', array('matches' => $matches, 'data' => $data));
  }

  public function listMatchings() {
    $matches = DB::table('matches')->whereIn('status', [31, 32])->get();
    foreach ($matches as $match) {
      $applicant = Applicant::where('aid', '=', $match->aid)->first();
      $match->applicant_name = $applicant->first_name . " " . $applicant->last_name;
      $program = Program::where('pid', '=', $match->pid)->first();
      $provider = Provider::find($program->proid);
      $match->program_name = $program->name;
      $match->provider_name = $provider->name;
      $match->status_text = Code::where('code', '=', $match->status)->first()->value;

      $scopes = config('kitamatch_config.care_scopes');
      $starts = config('kitamatch_config.care_starts');

      $pid_split = explode("_", $match->pid);
      $pid = $pid_split[0];
      $match->start = $starts[$pid_split[1]];#
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
    $Applicant = new Applicant;
    $Program = new Program;
    $Provider = new Provider;
    $Matching = new Matching;
    $data = array();
    $applicants = Applicant::all();
    $programs = Program::all();
    $providers = Provider::all();
    $matching = $Matching->getActiveMatches();
    $data['applicants'] = $applicants;
    $data['applicantsCount'] = count($applicants);
    $data['applicantsVerified'] = count(Applicant::whereIn('status', [22, 25, 26])->get());
    $data['applicantsFinal'] = count(Applicant::where('status', '=', 26)->get());

    $nonMatches = array();
    foreach ($applicants as $applicant) {
      $filter = DB::table('matches')->where('aid', '=', $applicant->aid)->first();
      if (count($filter) == 0) {
        $nonMatches[$applicant->aid]['first_name'] = $applicant->first_name;
        $nonMatches[$applicant->aid]['last_name'] = $applicant->last_name;
        $nonMatches[$applicant->aid]['birthday'] = $applicant->birthday;
        $nonMatches[$applicant->aid]['gender'] = $applicant->gender;
        $nonMatches[$applicant->aid]['age_cohort'] = $applicant->age_cohort;
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

  public function exportAssignedApplicants()
  {
    $matches = $this->listMatchings();
    $matches_array[] = array('aid','Bewerber','ServiceID','Kita', 'Kitagruppe', 'Status','Umfang', 'Beginn');

    $scopes = config('kitamatch_config.care_scopes');
    $starts = config('kitamatch_config.care_starts');

    foreach($matches as $match){
      
      $id_to_split = explode("_", $match->pid);
            $p_id = $id_to_split[0];
            $start = $starts[$id_to_split[1]];
            $scope = $scopes[$id_to_split[2]];

      $matches_array[] = array(
        'BewerberID'=> $match->aid,
        'Bewerber'=> $match->applicant_name, 
        'ServiceID'=>$match->pid,
        'Kita' => $match->provider_name, 
        'Kitagruppe' => $match->program_name,
        'Status' => $match->status_text,
        'Umfang' => $scope,
        'Beginn' => $start
      );

    };
    Excel::create('Zuordnungen', function($excel) use($matches_array){
      $excel->setTitle('Zuordnungen');
      $excel->sheet('Zuordnungen', function($sheet) use ($matches_array){
        $sheet->fromArray($matches_array, null, 'A1', false, false);
      });
    })->download('xlsx');
  }

  public function exportUnassignedApplicants()
  {
    $data = $this->generateDashboard();
    $nonMatches_array[] = array('Name', 'Geburtsdatum', 'Geschlecht','Age Cohort');

    foreach($data['non-matches'] as $nonMatch){
      
      $nonMatches_array[] = array(
        'Name' => $nonMatch['first_name'].' '.$nonMatch['last_name'],
        'Geburtsdatum' => $nonMatch['birthday']->format('d.m.Y'),
        'Geschlecht'=> $nonMatch['gender'],
        'age_cohort' => $nonMatch['age_cohort'] 
      );

    };
    Excel::create('Nicht zugeordnete Bewerber', function($excel) use($nonMatches_array){
      $excel->setTitle('Nicht zugeordnete Bewerber');
      $excel->sheet('Nicht zugeordnete Bewerber', function($sheet) use ($nonMatches_array){
        $sheet->fromArray($nonMatches_array, null, 'A1', false, false);
      });
    })->download('xlsx');
  }

}
