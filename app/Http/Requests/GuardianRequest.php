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
 | Update Guardian Request
 |--------------------------------------------------------------------------
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Guardian;

/**
* This class handles the update request of guardians
*/
class GuardianRequest extends FormRequest
{
  /**
  * Determine if the user is authorized to make this request.
  *
  * @return bool
  */
  public function authorize() {
    /*if ($guardian->uid == $this->user()->id) {
      return true;
    } else {
      return false;
    }*/
    return true;
  }

  /**
  * Validation rules that apply to the request.
  *
  * @return array
  */
  public function rules() {
    return [
      'firstName' => 'required|string|min:2',
      'lastName' => 'required|string|min:2',
      'phone' => 'nullable|string|min:6',
      'address' => 'nullable|string|min:4',
      'plz' => 'nullable|numeric|min:5',
      'city' => 'nullable|string|min:2',
      'parentalStatus' => 'nullable|numeric|min:820|max:824',
      'volumeOfEmployment' => 'nullable|numeric|min:830|max:833',
      'siblings' => 'nullable|numeric|min:840|max:841',
    ];
  }
}
