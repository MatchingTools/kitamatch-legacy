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
 | Applicant Model
 |--------------------------------------------------------------------------
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
* This model handles applicants
*/
class Applicant extends Model
{

  /**
  * Get all applicants by a guardian
  *
  * @param integer $gid Guardian-ID
  * @return Illuminate\Database\Eloquent\Collection applicants
  */
  public function getApplicantsByUid($uid) {
    $applicants = Applicant::where('uid', '=', $uid)->get();
    return $applicants;
  }

  /**
  * Get the corresponding guardian id of an applicant
  *
  * @param integer $aid Applicant-ID
  * @return integer
  */
  /*public function getGuardianIdByApplicant($aid) {
    $applicant = Applicant::where('aid', '=', $aid)->first();
    return $applicant->gid;
  }*/

  /**
  * Get all applicants with status 22 or 25
  *
  * @return Illuminate\Database\Eloquent\Collection applicants
  */
  public function getAll() {
    return (Applicant::whereIn('status', [22, 25])->get());
  }

  /**
  * The dates that should be available for Carbon
  *
  * @var array
  */
  protected $dates = [
    'created_at',
    'updated_at',
    'deleted_at',
    'birthday'
  ];

  public $primaryKey = 'aid';

}
