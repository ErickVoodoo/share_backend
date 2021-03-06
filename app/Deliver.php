<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deliver extends Model
{
  protected $keyType = 'char';
  public $timestamps = false;

  protected $table_name = 'delivers';
  protected $fillable = ['id', 'name'];
}
