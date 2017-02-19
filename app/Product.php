<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $table = 'products';
  protected $fillable = ['id', 'user_id', 'category_id', 'price', 'location_id', 'discount_id', 'title', 'description', 'delivers_id', 'images'];
  protected $hidden = ['location_id', 'category_id', 'user_id', 'discount_id'];

  public function product_tag() {
    return $this->hasMany('App\ProductTag');
  }

  public function tags() {
    return $this->belongsToMany('App\Tag', 'product_tags');
  }

  public function links() {
    return $this->hasMany('App\Link');
  }

  public function images() {
    return $this->hasMany('App\Image');
  }

  public function discount() {
    return $this->hasOne('App\Discount', 'id', 'discount_id');
  }

  public function category() {
    return $this->belongsTo('App\Category');
  }

  public function location() {
    return $this->belongsTo('App\Location'); //->select(array('city', 'street', 'lat', 'lon'))
  }

  public function user() {
    return $this->belongsTo('App\User')->select(array('id', 'name', 'logo'));
  }
}
