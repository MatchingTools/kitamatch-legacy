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
 | Program Model
 |--------------------------------------------------------------------------
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
* This model handles programs
*/
class Program extends Model
{
  /**
  * Get all programs with status 12 or 13 ordered by name
  *
  * @return Illuminate\Database\Eloquent\Collection programs
  */
  public function getAll() {
    return (Program::whereIn('status', [12, 13])->orderBy('name', 'asc')->get());
  }

  /**
  * Is a program coordinated?
  *
  * @param integer $pid Program-ID
  * @return boolean
  */
  public function isCoordinated($pid) {
    $res = Program::find($pid);
    return $res->coordination;
  }

  /**
  * Get all coordinated programs regardless of status
  *
  * @return Illuminate\Database\Eloquent\Collection programs
  */
  public function getCoordinated() {
    $programs = Program::where('coordination', '=', 1)->get();
    return $programs;
  }

  /**
  * Get the provider id of a program
  *
  * @param integer $pid Program-ID
  * @return integer
  */
  public function getProviderId($pid) {
    $program = Program::where('pid', '=', $pid)->first();
    return $program->proid;
  }

  /**
  * Get a program by its corresponding user id
  *
  * @param integer $uid User-ID
  * @return Illuminate\Database\Eloquent\Collection program
  */
  public function getProgramByUid($uid) {
    $program = Program::where('uid', '=', $uid)->firstOrFail();
    return $program;
  }

  /**
  * Get all programs assosciated with a certain provider
  *
  * @param integer $proid Provider-ID
  * @return Illuminate\Database\Eloquent\Collection programs
  */
  public function getProgramsByProid($proid) {
    $programs = Program::where('proid', '=', $proid)->get();
    return $programs;
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

  public $primaryKey = 'pid';
}
