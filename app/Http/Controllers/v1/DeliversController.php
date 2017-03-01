<?php

namespace App\Http\Controllers\v1;

use App\Deliver;
use Response;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class DeliversController extends App_Controller
{

    // CHECKED ALL ROUTES

    public function index()
    {
      $delivers = Deliver::get();

      return Response::json([
        'response' => [
          'delivers' => $delivers,
        ],
      ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function store(Request $request) {
      $deliver = Deliver::create([
        'id' => Uuid::uuid4()->toString(),
        'name' => $request->input('name'),
      ]);

      return Response::json([
        'response' => [
          'deliver' => $deliver
        ],
      ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function show($id)
    {
      $deliver = Deliver::find($id);

      return Response::json([
        'response' => [
          'deliver' => $deliver,
        ],
      ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
