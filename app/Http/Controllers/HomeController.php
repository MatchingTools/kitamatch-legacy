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
 | Home Controller
 |--------------------------------------------------------------------------
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Guardian;
use App\Provider;
use App\Program;
use Illuminate\Support\Facades\Hash;

/**
* This controller is responsible for the home site, mainly redirecting to different account types.
*/
class HomeController extends Controller
{
  /**
   * Create a new controller instance, handle authentication
   *
   * @return void
   */
  public function __construct() {
      $this->middleware('auth');
  }

  public function showChangePasswordForm(){
    return view('auth.changepassword');
  }

  public function changePassword(Request $request){

    if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
        // The passwords matches
        return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
    }
    if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
        //Current password and new password are same
        return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
    }
    $validatedData = $request->validate([
        'current-password' => 'required',
        'new-password' => 'required|string|min:6|confirmed',
    ]);
    //Change Password
    $user = Auth::user();
    $user->password = bcrypt($request->get('new-password'));
    $user->save();
    Auth::logout();
    return redirect()->route('login')->with("success","Password changed successfully !");
    
  }

  /**
  * Detects authentication and redirect to the different user interfaces. If there is no auth, redirect to home view.
  *
  * @return \Illuminate\Http\Response Guaridan/Program/Provider/Admin
  */
  public function index() {
    $user = Auth::user();
    if ($user->account_type == 1) {
      $Applicant = new Applicant;
      $applicant = $Applicant->getApplicantsByUid($user->id);
      return redirect()->action('ApplicantController@show', [$applicant->aid]);
    } else if ($user->account_type == 2 || $user->account_type == 3) {
      $Program = new Program;
      $program = $Program->getProgramByUid($user->id);
      return redirect()->action('ProgramController@show', [$program->pid]);
    } else if ($user->account_type == 4) {
      $Provider = new Provider;
      $provider = $Provider->getProviderByUid($user->id);
      return redirect()->action('ProviderController@show', [$provider->proid]);
    } else if ($user->account_type == 5) {
      return redirect()->action('AdminController@index');
    } else {
      return view('home');
    }
  }

}
