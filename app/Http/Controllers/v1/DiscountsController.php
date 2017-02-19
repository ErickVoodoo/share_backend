<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Discount;

class DiscountsController extends App_Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $discounts = Discount::get();

    return Response::json([
      'response' => [
        'discounts' => $discounts,
      ],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
      //
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Discount  #discount
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $discount = Discount::find($id);

    return Response::json([
      'response' => [
        'discount' => $discount,
      ],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Discount  $discount
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
      //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Discount $discount
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
      //
  }
}
