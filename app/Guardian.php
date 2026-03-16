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
 | Guardian Model
 |--------------------------------------------------------------------------
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
* This model handles guardians
*/
class Guardian extends Model
{

  /**
  * Get a guardian by its user number
  *
  * @param integer $uid User-ID
  * @return Illuminate\Database\Eloquent\Collection guardian
  */
  public function getGuardianByUid($uid) {
    $guardian = Guardian::where('uid', '=', $uid)->firstOrFail();
    return $guardian;
  }

  public $primaryKey = 'gid';
}
