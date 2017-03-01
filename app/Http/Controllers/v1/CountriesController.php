<?php

namespace App\Http\Controllers\v1;
use App\Country;
use Response;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class CountriesController extends App_Controller
{

  // CHECKED ALL ROUTES

  public function index() {
    $countries = Country::get();

    return Response::json([
      'response' => [
        'countries' => $countries,
      ],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  public function store(Request $request) {
    $country = Country::create([
      'id' => Uuid::uuid4()->toString(),
      'name' => $request->input('name'),
    ]);

    return Response::json([
      'response' => [
        'country' => $country
      ],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  public function show($id) {
    $country = Country::find($id);

    return Response::json([
      'response' => [
        'country' => $country,
      ],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }
}
