<?php

namespace App\Http\Controllers\v1;

use Response;
use App\Tag;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class TagsController extends App_Controller
{

    // CHECKED ALL ROUTES

    public function index()
    {
      $tags = Tag::get();

      return Response::json([
        'response' => [
          'tags' => $tags,
        ],
      ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function store(Request $request) {
      $tag = Tag::create([
        'id' => Uuid::uuid4()->toString(),
        'name' => $request->input('name'),
      ]);

      return Response::json([
        'response' => [
          'tag' => $tag
        ],
      ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function show($id)
    {
      $tag = Tag::find($id);

      return Response::json([
        'response' => [
          'tag' => $tag,
        ],
      ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
