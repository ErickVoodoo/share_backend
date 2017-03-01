<?php

namespace App;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    protected $keyType = 'char';
    protected $table = 'roles';
    protected $fillable = ['id', 'name', 'display_name', 'description'];
}
