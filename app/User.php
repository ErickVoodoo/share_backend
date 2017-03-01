<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable, EntrustUserTrait;

    protected $table_name = 'users';
    protected $fillable = ['id', 'login', 'email', 'api_token', 'password', 'country_id', 'plan_id', 'name', 'description', 'logo'];
    protected $hidden = ['password'];
    protected $keyType = 'char';

    public function products() {
      return $this->hasMany('App\Product');
    }

    public function country() {
      return $this->belongsTo('App\Country');
    }

    public function locations() {
      return $this->hasMany('App\Location');
    }

    public function plan() {
      return $this->belongTo('App\Plan');
    }

    public function role() {
      return $this->hasMany('App\RoleUser');
    }
}
