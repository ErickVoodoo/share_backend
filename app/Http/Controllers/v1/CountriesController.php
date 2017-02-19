<?php

namespace App\Http\Controllers\v1;
use App\Country;
use Response;
use Illuminate\Http\Request;

class CountriesController extends App_Controller
{
  public function index() {
    $countries = Country::get();

    return Response::json([
      'response' => [
        'countries' => $countries,
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
