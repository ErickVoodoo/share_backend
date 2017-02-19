<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'discounts';
    protected $fillable = ['id', 'value', 'promo'];

    public function product() {
      return $this->hasOne('App\Product');
    }
}
