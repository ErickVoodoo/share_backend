<?php

namespace App\Http\Controllers\v1;
use Response;
use App\User;
use App\Category;
use Illuminate\Http\Request;

class CategoriesController extends App_Controller
{
  public function index() {
    $categories = Category::all();

    return Response::json([
      'response' => $categories,
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  public function show($id) {
    $category = Category::find($id);

    if (!$category) {
      return Response::json([
        'error' => [
          'fields' => [
            'category' => 'The category with id ' . $id . ' not found',
          ],
        ],
      ], 404, [], JSON_UNESCAPED_UNICODE);
    }

    return Response::json([
      'response' => $category,
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }
}
