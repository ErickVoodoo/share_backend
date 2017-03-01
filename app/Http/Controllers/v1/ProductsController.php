<?php

namespace App\Http\Controllers\v1;
use Response;
use Auth;
use Input;
use App\Discount;
use App\Link;
use App\User;
use App\Product;
use App\Deliver;
use App\Tag;
use App\Category;
use App\Image;
use App\ProductTag;
use App\Location;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use DB;
use JWTAuth;

class ProductsController extends App_Controller
{

  // CHECKED ALL ROUTES

  public function index(Request $request) {
    $this->validate($request, [
      'offset' => 'max:128|numeric',
      'size' => 'max:128|numeric',
      'user_id' => 'max:128',
      'category_id' => 'max:128',
      'discount_id' => 'max:128',
      'title' => 'max:128',
      'deliver_id' => 'max:128',
      'tags' => 'max:256',
      'country_id' => 'max: 128',
      'city' => 'max:64',
      'street' => 'max: 128',
    ]);

    $where = array(
      'offset' => $request->input('offset'),
      'size' => $request->input('size'),

      'user_id' => $request->input('user_id'),
      'category_id' => $request->input('category_id'),
      'discount_id' => $request->input('discount_id'),
      'title' => $request->input('title'),
      'deliver_id' => $request->input('deliver_id'),
      'tags' => $request->input('tags'),
      'country_id' => $request->input('country'),
      'city' => $request->input('city'),
      'street' => $request->input('street'),
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

    if ($where['deliver_id']) {
      $products = $products->where('deliver_id', $where['deliver_id']);
    }

    if ($where['tags']) {
      $products = $products->whereHas('tags', function($query) use ($request) {
        $query->whereIn('name', $request->input('tags'));
      });
    }

    if ($where['country_id']) {
      $products = $products->whereHas('user', function($query) use ($request) {
        $query->whereIn('country_id', $request->input('country_id'));
      });
    }

    if ($where['city'] || $where['street']) {
      $products = $products->whereHas('location', function($query) use ($request) {
        $query->where('city', $request->input('city'));

        if ($request->input('street')) {
          $query->where('street', 'like', '%' . $request->input('street') . '%' );
        }
      });
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
      'category_id' => 'required|max:255',
      'discount_id' => 'max:255',
      'title' => 'required|max:128',
      'description' => 'max:255',
      'deliver_id' => 'max:255',
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

    if (!$request->input('category_id') || !Category::find($request->input('category_id'))) {
      return Response::json([
        'error' => [
          'fields' => [
            'category' => 'Can\'t find the category',
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

    $deliver = (object) ['id' => null];
    if ($request->input('deliver_id') && Deliver::find($request->input('deliver_id'))) {
      $deliver = Deliver::find($request->input('deliver_id'));
    }

    $discount = (object) ['id' => null];
    if ($request->input('discount_value') && $request->input('discount_promo')) {
      $discount = Discount::create([
        'id' => Uuid::uuid4()->toString(),
        'value' => $request->input('discount_value'),
        'promo' => $request->input('discount_promo'),
      ]);
    }

    $fill = [
      'id' => Uuid::uuid4()->toString(),
      'user_id' => JWTAuth::parseToken()->authenticate()->id,
      'category_id' => $request->input('category_id'),
      'title' => $request->input('title'),
      'description' => $request->input('description'),
      'price' => $request->input('price'),
      'location_id' => $request->input('location_id'),
    ];

    if ($discount->id) {
      $fill['discount_id'] = $discount->id;
    }

    if ($deliver->id) {
      $fill['deliver_id'] = $deliver->id;
    }

    $product = Product::create($fill);

    $tags = $request->input('tags');

    if ($tags) {
      foreach ($tags as $value) {
        $current_tag = Tag::where('name', $value)->first();
        if (!$current_tag) {
          $current_tag = Tag::create([
            'id' => Uuid::uuid4()->toString(),
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
      $current_link = new Link(['id' => Uuid::uuid4()->toString(), 'url' => $value]);
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
      'response' => [
        'product' => $new_product
      ],
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
      'category_id' => 'required|max:255',
      'discount_id' => 'max:255',
      'title' => 'required|max:128',
      'description' => 'max:255',
      'deliver_id' => 'max:255',
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

    $discount = Discount::find($product->discount_id);
    $discount_id = null;
    if ($discount && $discount->id) {
      $discount_id = $discount->id;
    }

    if (!$request->input('discount_value') || !$request->input('discount_promo')) {
      $discount = (object) ['id' => null];
    } else {
      $new_discount = [
        'id' => Uuid::uuid4()->toString(),
        'value' => $request->input('discount_value'),
        'promo' => $request->input('discount_promo'),
      ];

      if (!$product->discount_id) {
        $discount = Discount::create($new_discount);
      } else {
        $discount->fill(['value' => $new_discount['value'], 'promo' => $new_discount['promo']])->save();
      }
    }

    $fill = [
      'user_id' => JWTAuth::parseToken()->authenticate()->id,
      'category_id' => $request->input('category_id'),
      'title' => $request->input('title'),
      'price' => $request->input('price'),
      'description' => $request->input('description'),
      'location_id' => $request->input('location_id')
    ];

    $deliver = Deliver::find($request->input('deliver_id'));
    if (!$deliver || !$request->input('deliver_id')) {
      $deliver = (object) ['id' => null];
    }

    $fill['deliver_id'] = $deliver->id;
    $fill['discount_id'] = $discount->id;

    $product->fill($fill);

    $tags = $request->input('tags');

    if ($tags) {
      ProductTag::where('product_id', $product->id)->delete();
      foreach ($tags as $value) {
        $current_tag = Tag::where('name', $value)->first();
        if (!$current_tag) {
          $current_tag = Tag::create([
            'id' => Uuid::uuid4()->toString(),
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
        $current_link = new Link(['id' => Uuid::uuid4()->toString(), 'url' => $value]);
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

    $images = $request->input('images');

    $product_images = Image::where('product_id', $product->id)->get();
    foreach ($product_images as $image) {
      $image->fill(array('product_id' => null ))->save();
    }

    if ($images) {
      foreach ($images as $value) {
        $image = Image::where('id', $value)->first();
        if ($image) {
          $image->fill(array('product_id' => $product->id ))->save();
        }
      }
    }

    $product->save();

    if ($discount_id && !$request->input('discount_promo') && !$request->input('discount_value')) {
      Discount::find($discount_id)->delete();
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
      'response' => [
        'product' => $new_product
      ],
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

    $product->delete();
    $product->product_tag()->delete();
    $product->images()->delete();
    $product->links()->delete();
    $product->discount()->delete();


    return Response::json([
      'response' => ['id' => $product->id],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }
}
