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
 | Capacity Model
 |--------------------------------------------------------------------------
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
* This model handles the (status-)codes
*/
class Capacity extends Model
{

  public function getCapacity($sid) {
    // service id (sid): pid_start_scope
    $sid_explode = explode("_", $sid);
    $pid = $sid_explode[0];
    $start = $sid_explode[1];
    $scope = $sid_explode[2];

    $capacity = Capacity::where('pid', '=', $pid)
      ->where('care_start', '=', $start)
      ->where('care_scope', '=', $scope)
      ->first();
    return $capacity->capacity;
  }

  public function getScopeCapacity($pid, $start, $scope){
    $capacity = Capacity::where('pid', '=', $pid)->where('care_start', '=', $start)->where('care_scope', '=', $scope)->first();
    return $capacity->capacity;
  }

  public $primaryKey = 'id';
  protected $table = 'capacities';
}
