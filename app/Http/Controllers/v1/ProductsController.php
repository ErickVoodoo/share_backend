<?php

namespace App\Http\Controllers\v1;
use Response;
use Auth;
use Input;
use App\Discount;
use App\Link;
use App\User;
use App\Product;
use App\Tag;
use App\Image;
use App\ProductTag;
use App\Location;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use DB;
use JWTAuth;

class ProductsController extends App_Controller
{
  public function index(Request $request) {
    $this->validate($request, [
      'offset' => 'max:128|numeric',
      'size' => 'max:128|numeric',
      'user_id' => 'max:128|numeric',
      'category_id' => 'max:128|numeric',
      'discount_id' => 'max:128|numeric',
      'title' => 'max:128',
      'delivers_id' => 'max:128|numeric',
    ]);

    $where = array(
      'offset' => $request->input('offset'),
      'size' => $request->input('size'),
      'user_id' => $request->input('user_id'),
      'category_id' => $request->input('category_id'),
      'discount_id' => $request->input('discount_id'),
      'title' => $request->input('title'),
      'delivers_id' => $request->input('delivers_id'),
    );

    $products = new Product;

    if ($where['user_id']) {
      $products = $products->where('user_id', $where['user_id']);
    }

    if ($where['category_id']) {
      $products = $products->where('category_id', $where['category_id']);
    }

    if ($where['discount_id']) {
      $products = $products->where('discount_id', $where['discount_id']);
    }

    if ($where['title']) {
      $products = $products->where('title', 'like', '%' .$where['title'] . '%' );
    }

    if ($where['delivers_id']) {
      $products = $products->where('delivers_id', $where['delivers_id']);
    }

    $total = $products->get()->count();

    if ($where['size']) {
      $products = $products->take($where['size']);
    }

    if ($where['offset']) {
      $products = $products->offset($where['offset']);
    }

    $products = $products->with(
      'user',
      'category',
      'discount',
      'location',
      'tags',
      'links',
      'images'
      )->get();

    return Response::json([
      'response' => [
        'products' => $products,
        'meta' => [
          'total' => (integer) $total,
          'size' => (integer) $where['size'],
          'offset' => (integer) $where['offset'],
        ],
      ],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  public function store(Request $request) {
    $this->validate($request, [
      'category_id' => 'required|max:255|numeric',
      'discount_id' => 'max:255|numeric',
      'title' => 'required|max:128',
      'description' => 'max:255',
      'delivers_id' => 'max:255',
      'location_id' => 'required|max:255',
      'price' => 'min:1|max:64',
      'links' => 'required',
      'discount_value' => 'max:255',
      'discount_promo' => 'max:255',
    ]);

    if (!$request->input('location_id') || !Location::where('user_id', JWTAuth::parseToken()->authenticate()->id)->find($request->input('location_id'))) {
      return Response::json([
        'error' => [
          'fields' => [
            'location' => 'Can\'t find the location',
          ],
        ],
      ], 422, [], JSON_UNESCAPED_UNICODE);
    }

    $links = $request->input('links');

    if (!$links) {
      return Response::json([
        'error' => [
          'fields' => [
            'links' => 'Please fill at least one link',
          ],
        ],
      ], 422, [], JSON_UNESCAPED_UNICODE);
    } else if (Link::whereIn('url', $links)->get()->count()) {
      return Response::json([
        'error' => [
          'fields' => [
            'links' => 'The link has been already used',
          ],
        ],
      ], 422, [], JSON_UNESCAPED_UNICODE);
    }

    $discount = Discount::create([
      'id' => Uuid::uuid4()->toString(),
      'value' => $request->input('discount_value'),
      'promo' => $request->input('discount_promo'),
    ]);

    $product = Product::create([
      'user_id' => JWTAuth::parseToken()->authenticate()->id,
      'category_id' => $request->input('category_id'),
      'title' => $request->input('title'),
      'description' => $request->input('description'),
      'price' => $request->input('price'),
      'delivers_id' => $request->input('delivers_id'),
      'discount_id' => $discount->id,
      'location_id' => $request->input('location_id'),
    ]);

    $tags = $request->input('tags');

    if ($tags) {
      foreach ($tags as $value) {
        $current_tag = Tag::where('name', $value)->first();
        if (!$current_tag) {
          $current_tag = Tag::create([
            'name' => $value,
          ]);
        }

        ProductTag::create([
          'product_id' => $product->id,
          'tag_id' => $current_tag->id,
        ]);
      }
    }

    foreach ($links as $value) {
      $current_link = new Link(['url' => $value]);
      $product->links()->save($current_link);
    }

    $images = $request->input('images');

    if ($images) {
      foreach ($images as $value) {
        $image = Image::where('id', $value)->first();
        if ($image) {
          $image->fill(array('product_id' => $product->id ))->save();
        }
      }
    }

    $new_product = Product::with(
          'user',
          'category',
          'discount',
          'location',
          'tags',
          'links',
          'images'
          )->find($product->id);

    return Response::json([
      'response' => ['product' => $new_product],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  public function show($id) {
    $product = Product::with(
      'user',
      'category',
      'discount',
      'location',
      'tags',
      'links',
      'images'
      )->find($id);

    if (!$product) {
      return Response::json([
        'error' => [
          'message' => 'The product with id ' . $id . ' not found',
        ],
      ], 404, [], JSON_UNESCAPED_UNICODE);
    }

    return Response::json([
      'response' => ['product' => $product],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  public function update(Request $request, $id) {
    $this->validate($request, [
      'category_id' => 'required|max:255|numeric',
      'discount_id' => 'max:255|numeric',
      'title' => 'required|max:128',
      'description' => 'max:255',
      'delivers_id' => 'max:255',
      'location_id' => 'required|max:255',
      'price' => 'min:1|max:64',
      'links' => 'required',
      'discount_value' => 'max:255',
      'discount_promo' => 'max:255',
    ]);

    $product = Product::where('user_id', JWTAuth::parseToken()->authenticate()->id)->find($id);

    if (!$product) {
      return Response::json([
        'error' => [
          'message' => 'The product with id ' . $id . ' not found',
        ],
      ], 404, [], JSON_UNESCAPED_UNICODE);
    }

    if (!$request->input('location_id') || !Location::where('user_id', JWTAuth::parseToken()->authenticate()->id)->find($request->input('location_id'))) {
      return Response::json([
        'error' => [
          'fields' => [
            'location' => 'Can\'t find the location',
          ],
        ],
      ], 422, [], JSON_UNESCAPED_UNICODE);
    }

    $product->fill([
      'user_id' => JWTAuth::parseToken()->authenticate()->id,
      'category_id' => $request->input('category_id'),
      'title' => $request->input('title'),
      'price' => $request->input('price'),
      'description' => $request->input('description'),
      'delivers_id' => $request->input('delivers_id'),
      'location_id' => $request->input('location_id')
    ]);

    $tags = $request->input('tags');

    if ($tags) {
      ProductTag::where('product_id', $product->id)->delete();
      foreach ($tags as $value) {
        $current_tag = Tag::where('name', $value)->first();
        if (!$current_tag) {
          $current_tag = Tag::create([
            'name' => $value,
          ]);
        }

        ProductTag::create([
          'product_id' => $product->id,
          'tag_id' => $current_tag->id,
        ]);
      }
    }

    $links = $request->input('links');

    if ($links) {
      Link::where('product_id', $product->id)->delete();
      foreach ($links as $value) {
        $current_link = new Link(['url' => $value]);
        $product->links()->save($current_link);
      }
    } else {
      return Response::json([
        'error' => [
          'fields' => [
            'links' => 'Please fill at least one link',
          ],
        ],
      ], 422, [], JSON_UNESCAPED_UNICODE);
    }

    $discount = Discount::find($product->discount_id);
    $new_discount = [
      'value' => $request->input('discount_value'),
      'promo' => $request->input('discount_promo'),
    ];

    if ($discount->promo !== $new_discount['promo'] ||
        $discount->vlaue !== $new_discount['value']) {
        $discount->fill($new_discount)->save();
    }

    $images = $request->input('images');

    if ($images) {
      $product_images = Image::where('product_id', $product->id)->get();
      foreach ($product_images as $image) {
        $image->fill(array('product_id' => null ))->save();
      }
      foreach ($images as $value) {
        $image = Image::where('id', $value)->first();
        if ($image) {
          $image->fill(array('product_id' => $product->id ))->save();
        }
      }
    }

    $product->save();
    $new_product = Product::with(
          'user',
          'category',
          'discount',
          'location',
          'tags',
          'links',
          'images'
          )->find($product->id);

    return Response::json([
      'response' => ['product' => $new_product],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  public function destroy($id) {
    $product = Product::where('user_id', JWTAuth::parseToken()->authenticate()->id)
      ->find($id);

    if (!$product) {
      return Response::json([
        'error' => [
          'message' => 'The product with id ' . $id . ' not found',
        ],
      ], 404, [], JSON_UNESCAPED_UNICODE);
    }

    $product->product_tag()->delete();
    $product->images()->delete();
    $product->links()->delete();
    $product->discount()->delete();
    $product->delete();

    return Response::json([
      'response' => ['id' => $product->id],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }
}
