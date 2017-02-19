<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    protected $tagle = 'plans';
    protected $fillable = ['id', 'cost', 'period', 'name'];

    public function user() {
      return $this->hasOne('App\User');
    }
}
