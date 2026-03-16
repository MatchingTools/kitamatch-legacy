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
 | Applicant Request
 |--------------------------------------------------------------------------
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
* This class handles the requests of applicants
*/
class ApplicantRequest extends FormRequest
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
      //'firstName' => 'required|string|min:2',
      //'lastName' => 'required|string|min:2',
      'birthday' => 'required|date|date_format:Y-m-d',
      'gender' => 'required|string',
      'age_cohort' => 'required'
    ];
  }
}
