<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Program;


class Config extends Model
{

    public function getConfigByName($name){
      $kita_config =DB::table('kita_config')
      ->where('kita_config.config_name', '=', $name)
      ->get();

    return $kita_config;
    }

    //
    public $primaryKey = 'id';
}
