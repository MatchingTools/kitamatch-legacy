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
 | Provider Controller
 |--------------------------------------------------------------------------
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\ProviderRequest;
use App\Provider;
use App\Program;

/**
* This controller handles programs: the creation of new programs and update of existing ones, status changes and activity check for uncoordinated programs.
*/
class ProviderController extends Controller
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
  * Store a new provider
  *
  * @param App\Http\Requests\ProviderRequest $request
  * @return App\Provider
  */
  public function store(ProviderRequest $request) {
    $provider = new Provider;
    $provider->name = $request->name;
    $provider->uid = $request->uid;
    $provider->address = $request->address;
    $provider->city = $request->city;
    $provider->plz = $request->plz;
    $provider->phone = $request->phone;
    $provider->status = 61;
    $provider->save();
    return $provider;
  }

  /**
  * Store a program on user registration side. Right now every program is set valid by default.
  *
  * @param Illuminate\Http\Request $request request
  * @return App\Provider
  */
  public function storeByUser(Request $request) {
    $provider = new Provider;
    $provider->uid = $request->uid;
    $provider->status = 61;
    $provider->save();
    return $provider;
  }

  /**
  * Show a provider
  *
  * @param integer $proid Provider-ID
  * @return view provider.edit
  */
  public function show($proid) {
    $provider = Provider::findOrFail($proid);
    $Program = new Program;
    $programs = $Program->getProgramsByProid($proid);
    return view('provider.edit', array('provider' => $provider,
                                       'programs' => $programs));
  }

  /**
  * Edit a provider
  *
  * @param App\Http\Requests\ProviderRequest $request
  * @return action ProviderController@show
  */
  public function edit(ProviderRequest $request, $proid) {
    $request->request->add(['proid' => $proid]);
    $provider = $this->update($request);
    return redirect()->action('ProviderController@show', $provider->proid);
  }

  /**
  * Update a provider
  *
  * @param App\Http\Requests\ProviderRequest $request
  * @return App\Provider
  */
  public function update(ProviderRequest $request) {
    $provider = Provider::findOrFail($request->proid);
    $provider->name = $request->name;
    $provider->address = $request->address;
    $provider->city = $request->city;
    $provider->plz = $request->plz;
    $provider->phone = $request->phone;
    $provider->save();
    return $provider;
  }
}
