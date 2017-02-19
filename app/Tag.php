<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
  public $timestamps = false;

  protected $table = 'tags';
  protected $fillable = ['id', 'name'];
  protected $hidden = ['pivot'];

  public function product_tag() {
    return $this->belongsTo('App\ProductTag', 'tag_id');
  }

  public function product() {
    return $this->belongsToMany('App\Product', 'products');
  }
}
