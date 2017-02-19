<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
  protected $table_name = 'countries';
  protected $fillable = ['id', 'name'];

  public function product() {
    return $this->hasManyThrough('App\Product', 'App\User', 'country_id', 'user_id');
  }
}
