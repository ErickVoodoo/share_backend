<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = false;

    protected $table_name = 'categories';
    protected $fillable = ['id', 'name'];

    public function product() {
      return $this->hasOne('App\Product');
    }
}
