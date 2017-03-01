<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
  public $timestamps = false;
  public $incrementing = false;
  protected $primaryKey = null;
  protected $keyType = 'char';
  protected $table = 'role_user';
  protected $fillable = ['user_id', 'role_id'];

  public function user() {
    return $this->hasOne('App\User');
  }

  public function role() {
    return $this->hasOne('App\Role');
  }
}
