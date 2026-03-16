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
 | Guardian Controller
 |--------------------------------------------------------------------------
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\GuardianRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Guardian;
use App\Applicant;
use App\User;
use App\Code;
use App\Mail\GuardianVerified;
use Illuminate\Support\Facades\Mail;

/**
* This controller handles guardians: the creation of new and update of existing ones, as well as status changes (e.g. validation).
*/
class GuardianController extends Controller
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
  * Store new guardian
  *
  * @param App\Http\Requests\Request $request
  * @return App\Guardian
  */
  public function store(GuardianRequest $request) {
    $guardian = new Guardian;
    $guardian->uid = $request->uid;
    $guardian->first_name = $request->firstName;
    $guardian->last_name = $request->lastName;
    $guardian->status = 51;
    $guardian->address = $request->address;
    $guardian->city = $request->city;
    $guardian->plz = $request->plz;
    $guardian->phone = $request->phone;
    //$guardian->siblings = $request->siblings;
    //$guardian->parental_status = $request->parentalStatus;
    //$guardian->volume_of_employment = $request->volumeOfEmployment;
    $guardian->save();
    return $guardian;
  }

  /**
  * Store a program on user registration side. Right now every program is set valid by default.
  *
  * @param Illuminate\Http\Request $request request
  * @return App\Guardian
  */
  public function storeByUser(Request $request) {
    $guardian = new Guardian;
    $guardian->uid = $request->uid;
    $guardian->status = 51;
    $guardian->save();
    return $guardian;
  }

  /**
  * Show a single guardian view
  *
  * @param integer $gid Guardian-ID
  * @return view guardian.edit
  */
  public function show($gid) {
    $Applicant = new Applicant;
    $guardian = Guardian::findOrFail($gid);
    $applicants = $Applicant->getApplicantsByGid($gid);
    return view('guardian.edit', array('guardian' => $guardian,
                                       'applicants' => $applicants));
  }

  /**
  * Edit a single guardian
  *
  * @param App\Http\Requests\GuardianRequest $request request
  * @param integer $gid Guardian-ID
  * @return action GuardianController@show
  */
  public function edit(GuardianRequest $request, $gid) {
    $request->request->add(['gid' => $gid]);
    $guardian = $this->update($request);
    return redirect()->action('GuardianController@show', $guardian->gid);
  }

  /**
  * Update a single guardian
  *
  * @param App\Http\Requests\GuardianRequest $request request
  * @return App\Guardian
  */
  public function update(GuardianRequest $request) {
    $guardian = Guardian::findOrFail($request->gid);
    if ($request->firstName) { $guardian->first_name = $request->firstName; }
    if ($request->lastName) { $guardian->last_name = $request->lastName; }
    if ($request->status) { $guardian->status = $request->status; }
    if ($request->address) { $guardian->address = $request->address; }
    if ($request->city) { $guardian->city = $request->city; }
    if ($request->plz) { $guardian->plz = $request->plz; }
    if ($request->phone) { $guardian->phone = $request->phone; }
    //if ($request->siblings) { $guardian->siblings = $request->siblings; }
    //if ($request->parentalStatus) { $guardian->parental_status = $request->parentalStatus; }
    //if ($request->volumeOfEmployment) { $guardian->volume_of_employment = $request->volumeOfEmployment; }
    $guardian->save();
    return $guardian;
  }

  /**
  * Call 'all guardians' view
  *
  * @return view guardian.all
  */
  public function all() {
    $guardians = Guardian::all();
    foreach ($guardians as $guardian) {
      $user = User::where('id', '=', $guardian->uid)->first();
      $guardian->email = $user->email;
      $guardian->status_description = Code::where('code', '=', $guardian->status)->first()->value;
      /*$siblings_description = Code::where('code', '=', $guardian->siblings)->first();
      $parental_status_description = Code::where('code', '=', $guardian->parental_status)->first();
      $volume_of_employment_description = Code::where('code', '=', $guardian->volume_of_employment)->first();
      if ($siblings_description) {
        $guardian->siblings_description = $siblings_description->value;
       } else {
        $guardian->siblings_description = "--";
      }
      if ($parental_status_description) {
        $guardian->parental_status_description = $parental_status_description->value;
      } else {
        $guardian->parental_status_description = "--";
      }
      if ($volume_of_employment_description) {
        $guardian->volume_of_employment_description = $volume_of_employment_description->value;
      } else {
        $guardian->volume_of_employment_description = "--";
      }*/
    }
    return view('guardian.all', array('guardians' => $guardians));
  }

  /**
  * Verify a single guardian including all associated applicants. Optional: send notification mail (App\Mail\GuardianVerified).
  *
  * @param integer $gid Guardian-ID
  * @return action GuardianController@all
  */
  public function verify($gid) {
    $Applicant = new Applicant;
    Guardian::where('gid', '=', $gid)->update(array('status' => '52'));
    //verfiy applicant(s)
    $applicants = $Applicant->getApplicantsByGid($gid);
    foreach ($applicants as $applicant) {
      app('App\Http\Controllers\ApplicantController')->setValid($applicant->aid);
    }
    //mail
    //$guardian = Guardian::where('gid', '=', $gid)->first();
    //$user = User::where('id', '=', $guardian->uid)->first();
    //Mail::to($user->email)->send(new GuardianVerifiedMail($guardian));
    return redirect()->action('GuardianController@all');
  }
}
