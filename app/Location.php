<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $keyType = 'char';
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'locations';
    protected $fillable = ['id', 'user_id', 'city', 'street', 'lon', 'lat'];
    protected $hidden = ['user_id'];

    public function product() {
      return $this->hasOne('App\Product');
    }

    public function user() {
      return $this->belongsTo('App\User');
    }
}
