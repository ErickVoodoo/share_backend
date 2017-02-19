<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
  public $timestamps = false;
  public $incrementing = false;

  protected $table = 'product_tags';
  protected $primaryKey = null;
  protected $fillable = ['product_id', 'tag_id'];

  protected $hidden = [
    'product_id',
  ];

  public function product() {
    return $this->hasOne('App\Product');
  }

  public function tag() {
    return $this->hasOne('App\Tag');
  }
}
