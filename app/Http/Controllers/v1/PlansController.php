<?php

namespace App\Http\Controllers\v1;

use App\Plan;
use Response;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class PlansController extends App_Controller
{

    // CHECKED ALL ROUTES

    public function index()
    {
        $plans = Plan::get();

        return Response::json([
          'response' => [
            'plans' => $plans,
          ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function store(Request $request) {
      $plan = Plan::create([
        'id' => Uuid::uuid4()->toString(),
        'cost' => $request->input('cost'),
        'period' => $request->input('period'),
        'name' => $request->input('name'),
      ]);

      return Response::json([
        'response' => [
          'plan' => $plan
        ],
      ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function show($id)
    {
      $plan = Plan::find($id);

      return Response::json([
        'response' => [
          'plan' => $plan,
        ],
      ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
