<?php

namespace App\Http\Controllers\v1;
use App\Location;
use App\Product;
use Illuminate\Http\Request;
use League\Fractal;
use Ramsey\Uuid\Uuid;
use Response;
use JWTAuth;

class LocationsController extends App_Controller
{
  // CHECKED ALL ROUTES

  public function index()
  {
    $locations = Location::where('user_id', JWTAuth::parseToken()->authenticate()->id)->get();

    return Response::json([
      'response' => [
        'locations' => $locations,
      ],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  public function store(Request $request) {
    $this->validate($request, [
      'city' => 'required|max:255',
      'street' => 'required|max:255',
      'lat' => 'required|max:255',
      'lon' => 'required|max:255',
    ]);

    $location = Location::create([
      'id' => Uuid::uuid4()->toString(),
      'user_id' => JWTAuth::parseToken()->authenticate()->id,
      'city' => $request->input('city'),
      'street' => $request->input('street'),
      'lat' => $request->input('lat'),
      'lon' => $request->input('lon'),
    ]);

    return Response::json([
      'response' => [
        'location' => $location
      ],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  public function show($id)
  {
    $location = Location::find($id);

    return Response::json([
      'response' => [
        'location' => $location,
      ],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  public function cities(Request $request)
  {
    $country_id = $request->input('country_id');

    $products = new Product();

    if ($country_id) {
      $products = $products->whereHas('user', function($query) use ($request) {
        $query->where('country_id', $request->input('country_id'));
      });
    }

    $products = $products->with('location')->get()->toArray();

    $cities = array_map(array($this, 'transformCity'), $products);

    return Response::json([
      'response' => [
        'cities' => array_unique($cities),
      ],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  public function addresses(Request $request)
  {
    $country = $request->input('country_id');
    $city = $request->input('city');
    $products = new Product();

    if ($country) {
      $products = $products->whereHas('user', function($query) use ($request) {
        $query->where('country_id', $request->input('country_id'));
      });
    }

    $not_filtered_cities = $products->with('location')->get()->toArray();

    if ($city) {
      $products = $products->whereHas('location', function($query) use ($request) {
        $query->where('city', $request->input('city'));
      });
    }

    $products = $products->with('location')->get()->toArray();

    $cities = array_map(array($this, 'transformCity'), $not_filtered_cities);
    $streets = array_map(array($this, 'transformStreet'), $products);

    return Response::json([
      'response' => [
        'cities' => array_unique($cities) ,
        'streets' => $streets ? array_unique($streets) : [],
      ],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  function transformCity($value) {
    return $value['location']['city'];
  }

  function transformStreet($value) {
    return $value['location']['street'];
  }
}
