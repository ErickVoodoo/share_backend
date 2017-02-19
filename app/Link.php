<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    protected $tagle = 'links';
    protected $primaryKey = null;
    protected $fillable = ['product_id', 'url'];
    protected $hidden = ['product_id'];

    public function product() {
      return $this->hasOne('App\Product');
    }
}
