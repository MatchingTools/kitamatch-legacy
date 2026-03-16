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
 | Capacity Controller
 |--------------------------------------------------------------------------
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Capacity;

/**
* This controller handles the criteria catalogue: creation, update.
*/
class CapacityController extends Controller
{
  /**
   * Create a new controller instance, handle authentication
   *
   * @return void
   */
  public function __construct() {
    $this->middleware('auth');
  }

  public function store(Request $request) {
    $capacity = new Capacity;
    $capacity->pid = $request->pid;
    $capacity->care_start = $request->care_start;
    $capacity->care_scope = $request->care_scope;
    $capacity->capacity = $request->capacity;
    $capacity->save();
  }

  public function storeByProgram($pid) {
    foreach(config('kitamatch_config.care_starts') as $key_start => $care_start) {
      foreach(config('kitamatch_config.care_starts') as $key_scope => $care_scope) {
        if ($key_start != -1 and $key_scope != -1) {
          $request = new Request();
          $request->setMethod('POST');
          $request->request->add([
            'pid' => $pid,
            'care_start' => $key_start,
            'care_scope' => $key_scope
          ]);
          $this->store($request);
        }
      }
    }
  }

  public function update(Request $request) {
    $capacity = Capacity::find($request->id);
    $capacity->capacity = $request->capacity;
    $capacity->save();
    return $capacity;
  }

  public function updateByProgram(Request $request) {
    $inputs = $request->input();
    $data['isSet'] = app('App\Http\Controllers\PreferenceController')->isSet();
    foreach($inputs as $key => $value) {
      if (strpos($key, 'capacity_') !== false) {
        $id = substr($key, 9);
        $capacity = Capacity::find($id);
        if(($capacity->capacity > $value) && ($data['isSet'] && (Auth::user()->account_type != 5))){
          return back()->withErrors("Sie koennen die Anzahl an freien Plaetzen nur erweitern, nicht reduzieren.");
        }
        $capacity->capacity = $value;
        $capacity->save();
      }
    }
  }

  public function getProgramCapacities($pid) {
    $capacities = Capacity::where('pid', '=', $pid) // default criteria of municipality
      ->orderBy('care_start', 'care_scope')
      ->get();

    return $capacities;
  }

  public function hasProgramCapacity($pid) {
    $capacities = Capacity::where('pid', '=', $pid)->get();
    if ($capacities->count() > 0) {
      return True;
    } else {
      return False;
    }
  }
}
