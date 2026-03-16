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
 | Program Controller
 |--------------------------------------------------------------------------
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Requests\ProgramRequest;
use App\Http\Controllers\Controller;
use App\Traits\GetPreferences;
use App\Program;
use App\Matching;
use App\Provider;
use App\User;
use App\Code;

/**
* This controller handles programs: the creation of new and update of existing ones, status changes and activity check for uncoordinated.
*/
class ProgramController extends Controller
{
  /**
  * Create a new controller instance, handle authentication
  *
  * @return void
  */
  public function __construct() {
    $this->middleware('auth');
  }

  /**
  * Call the 'add program' view. The function is called when providers add their programs. For private programs the entry was already created by the register controller.
  *
  * @param integer $proid Provider-ID
  * @return view program.add
  */
  public function addByProvider($proid) {
    $provider = Provider::findOrFail($proid);
    return view('program.add', array('provider' => $provider));
  }

  /**
  * Create program on provider side. While doing so, create a user entry for the new program with a automatic set password.
  *
  * @param App\Http\Requests\ProgramRequest $request request
  * @param integer $proid Provider-ID
  * @return action ProviderController@show
  */
  public function createByProvider(ProgramRequest $request, $proid) {
    $provider = Provider::findOrFail($proid);
    //create a new user for the program
    $requestUser = new Request();
    $requestUser->setMethod('POST');

    //is the program private?
    if ($request->type == 1) {
      //private
      $p_kind = 2;
      $accountType = 3;
    } else {
      //public
      $p_kind = 1;
      $accountType = 2;
    }

    /*$requestUser->request->add([
      'email' => $request->email,
      'password' => app('App\Http\Controllers\Auth\RegisterController')->generateStrongPassword(),
      'account_type' => $accountType
    ]);
    $user = app('App\Http\Controllers\Auth\RegisterController')->storeByProvider($requestUser);*/
    //store the program
    $request->request->add([
      'p_kind' => $p_kind,
      'proid' => $proid
    ]);
    $program = $this->store($request);

    return redirect()->action('ProviderController@show', $proid);
  }

  /**
  * Store a program. Right now every program is set valid by default.
  *
  * @param App\Http\Requests\ProgramRequest $request request
  * @return App\Program
  */
  public function store(ProgramRequest $request) {
    //Validation
    $program = new Program;
    $program->uid = $request->uid;
    $program->proid = $request->proid;
    $program->name = $request->name;
    $program->address = $request->address;
    $program->capacity = $request->capacity;
    $program->p_kind = $request->p_kind;
    $program->age_cohort = $request->age_cohort;
    $program->coordination = $request->coordination;
    if (!$request->coordination) { $program->coordination = 0; }
    if ($program->p_kind == 1) { $program->coordination = 1; }
    $program->address = $request->address;
    $program->plz = $request->plz;
    $program->city = $request->city;
    $program->phone = $request->phone;
    $program->status = 12;
    $program->save();
    //tmp
    $this->setValid($program->id);
    return $program;
  }

  /**
  * Store a program on user registration or provider side. Right now every program is set valid by default.
  *
  * @param Illuminate\Http\Request $request request
  * @return App\Program
  */
  public function storeByUser(Request $request) {
    $program = new Program;
    $program->uid = $request->uid;
    $program->p_kind = $request->p_kind;
    $program->proid = $request->proid;
    $program->status = 12;
    $program->coordination = $request->coordination;
    $program->save();
    return $program;
  }

  /**
  * Show a single program
  *
  * @param integer $pid Program-ID
  * @return view program.edit
  */
  public function show($pid) {
    $program = Program::find($pid);

    if (!app('App\Http\Controllers\CapacityController')->hasProgramCapacity($pid)) {
      // create capacity entries
      app('App\Http\Controllers\CapacityController')->storeByProgram($program->pid);
    }

    $capacities = app('App\Http\Controllers\CapacityController')->getProgramCapacities($pid);
    return view('program.edit', array('program' => $program, 'capacities' => $capacities));
  }

  /**
  * Show all programs
  *
  * @return view program.all
  */
  public function all() {
    $programs = Program::all();
    foreach ($programs as $program) {
      $program->provider_name = Provider::where('proid', '=', $program->proid)->first()->name;
      $program->status_description = Code::where('code', '=', $program->status)->first()->value;
      $program->coordination_description = ($program->coordination == 1) ? "Ja" : "Nein";
      $program->p_kind_description = ($program->p_kind == 1) ? "Öffentlich" : "Frei";
    }
    return view('program.all', array('programs' => $programs));
  }

  /**
  * Call the 'edit program' view
  *
  * @param App\Http\Requests\ProgramRequest $request
  * @param integer $pid Program-ID
  * @return view program.edit
  */
  public function edit(ProgramRequest $request, $pid) {
    $request->request->add(['pid' => $pid]);
    $program = $this->update($request);
    $capacities = app('App\Http\Controllers\CapacityController')->updateByProgram($request);
    return redirect()->action('ProgramController@show', ['pid' => $pid]);
  }

  /**
  * Update a program
  *
  * @param App\Http\Requests\ProgramRequest $request
  * @return App\Program
  */
  public function update(ProgramRequest $request) {
    $program = Program::find($request->pid);
    $program->name = $request->name;
    $user = User::where('id', '=', $program->uid)->first();
    $program->coordination = $request->coordination;
    $program->age_cohort = $request->age_cohort;
    //p_kind = 1, so coordination needs to be 1
    if (!$request->coordination) { $program->coordination = 0; }
    if ($program->p_kind == 1) { $program->coordination = 1; }
    $program->capacity = $request->capacity;
    $program->address = $request->address;
    $program->plz = $request->plz;
    $program->city = $request->city;
    $program->phone = $request->phone;
    $program->save();
    return $program;
  }

  /**
  * Delete a program
  *
  * @param App\Http\Requests\Request $request
  * @param integer $pid Program-ID
  * @return action ProgramController@all
  */
  public function delete(Request $request, $pid) {
    $program = program::find($pid);
    $program->delete();
    return redirect()->action('ProgramController@all');
  }

  /**
  * Get the current capacity of a program. Note: the capacity is not the overall number of free places of the program, but rather an updated number since it takes the final matches into account (-).
  *
  * @param integer $pid Program-ID
  * @return integer
  */
  public function getCapacity($pid) {
    $program = Program::find($pid);
    //minus final matches
    $Matching = new Matching();
    $countFinalsMatches = count(Matching::where('pid', '=', $pid)
        ->where('status', '=', 32)
        ->get());

		 return ($program->capacity - $countFinalsMatches);
	 }

  /**
  * Update program status to verified
  *
  * @param integer $pid Program-ID
  * @return void
  */
  public function setValid($pid) {
    Program::where('pid', '=', $pid)->update(array('status' => '12'));
  }

  /**
  * Find all inactive programs, no activity for at least 7 days, and update status to inactive (incl. mail). Inactive programs don't take part in the matching procedure, see MatchingController@prepareMatching().
  * (Programs with current capacity equal to zero, will also be set inactive).
  *
  * @return void
  */
  public function setNonActive() {
    //set not inactive, if is no  1 week after coordiantion starts
    if (strtotime(config('kitamatch_config.coordination_start_date'))
         < strtotime('+7 days')) {
      return;
    }
    $Program = new Program();
    $preferences = DB::table('preferences')
              ->whereRaw('updated_at >= DATE_ADD(CURDATE(),INTERVAL -7 DAY)')
              ->where('pr_kind', '=', 3)
              ->get();
    $programs = $Program->getAll();
    foreach ($programs as $program) {
      if ($preferences->contains('id_from', $program->pid)) {
        Program::where('pid', '=', $program->pid)->update(array('status' => '12'));
      } else {
        Program::where('pid', '=', $program->pid)->update(array('status' => '13'));
        //!Mail
      }
    }
  }

}
