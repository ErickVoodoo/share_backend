<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PermissionRole extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null;

    protected $table = 'permission_role';
    protected $fillable = ['permission_id', 'role_id'];

    public function user() {
      return $this->hasOne('App\User');
    }

    public function permission() {
      return $this->hasOne('App\Permission');
    }
}
