<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $keyType = 'char';
    // public $timestamps = false;
    public $incrementing = false;

    protected $table = 'images';
    protected $fillable = ['id', 'product_id'];
    protected $hidden = ['product_id'];

    public function product() {
      return $this->hasOne('App\Product');
    }
}
