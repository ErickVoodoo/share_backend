<?php

namespace App\Http\Controllers\v1;
use App\Location;
use Illuminate\Http\Request;
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
}
