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
 | Provider Model
 |--------------------------------------------------------------------------
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
* This model handles provider
*/
class Provider extends Model
{
  /**
  * Get the corresponding provider of an User by ID
  *
  * @param integer $uid User-ID
  * @return Illuminate\Database\Eloquent\Collection provider
  */
  public function getProviderByUid($uid) {
    $provider = Provider::where('uid', '=', $uid)->firstOrFail();
    return $provider;
  }

  public $primaryKey = 'proid';
  protected $table = 'providers';
}
