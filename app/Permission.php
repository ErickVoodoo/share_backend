<?php

namespace App;

use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
    protected $keyType = 'char';
    protected $table = 'permissions';
    protected $fillable = ['id', 'name', 'display_name', 'description'];
}
