<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Discount;
use Ramsey\Uuid\Uuid;
use Response;

class DiscountsController extends App_Controller
{

  // CHECKED ALL ROUTES

  public function index()
  {
    $discounts = Discount::get();

    return Response::json([
      'response' => [
        'discounts' => $discounts,
      ],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  public function store(Request $request) {
    $discount = Discount::create([
      'id' => Uuid::uuid4()->toString(),
      'value' => $request->input('value'),
      'promo' => $request->input('promo'),
    ]);

    return Response::json([
      'response' => ['discount' => $discount],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  public function show($id)
  {
    $discount = Discount::find($id);

    return Response::json([
      'response' => [
        'discount' => $discount,
      ],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }
}
