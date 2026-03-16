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
 | Preference Request
 |--------------------------------------------------------------------------
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
* This class handles the requests of preferences
*/
class PreferenceRequest extends FormRequest
{
  /**
  * Determine if the user is authorized to make this request.
  *
  * @return bool
  */
  public function authorize() {
      return true;
  }

  /**
  * Validation rules that apply to the request.
  *
  * @return array
  */
  public function rules() {
    return [
      'id_to' => 'required|numeric|min:1',
      'id_from' => 'required|numeric|min:1',
      'pr_kind' => 'required|numeric|min:1|max:3',
      'status' => 'required|numeric|min:-1|max:1',
    ];
  }
}
