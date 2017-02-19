<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
  public $timestamps = false;

  protected $table_name = 'password_resets';
  protected $fillable = [];
}
