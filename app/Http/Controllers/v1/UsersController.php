<?php

namespace App\Http\Controllers\v1;

use JWTAuth;
use Illuminate\Http\Request;
use App\User;
use App\Country;
use App\Plan;
use Response;

class UsersController extends App_Controller
{

    // CHECKED ALL ROUTES

    public function index(Request $request) {
      $users = new User;

      if (!JWTAuth::getToken() || (JWTAuth::getToken() && JWTAuth::parseToken()->authenticate()->login !== 'admin')) {
        $users = $users::select('id', 'country_id', 'name', 'description', 'logo', 'created_at');
      }

      $where = array(
        'country_id' => $request->input('country_id'),
        'name' => $request->input('name'),
        'size' => $request->input('size'),
        'offset' => $request->input('offset'),
      );

      if ($where['country_id']) {
        $users = $users->where('country_id', $where['country_id']);
      }

      if ($where['name']) {
        $users = $users->where('name', 'like', '%' .$where['name'] . '%' );
      }

      $total = $users->get()->count();

      if ($where['size']) {
        $users = $users->take($where['size']);
      }

      if ($where['offset']) {
        $users = $users->offset($where['offset']);
      }

      $users = $users->get();

      return Response::json([
        'response' => [
          'users' => $users,
          'meta' => [
            'total' => (integer) $total,
            'size' => (integer) $where['size'],
            'offset' => (integer) $where['offset'],
          ]],
      ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function show($id) {
        $user = new User;

        if (!JWTAuth::getToken() || (JWTAuth::getToken() && JWTAuth::parseToken()->authenticate()->login !== 'admin')) {
          $user = $user::select('id', 'country_id', 'name', 'description', 'logo', 'created_at');
        }

        $user = $user->where('id', $id)
          ->with(
            'products.tags',
            'products.links',
            'products.images',
            'products.discount',
            'products.location',
            'products.category'
          )->get();

        return Response::json([
          'response' => [
            'user' => $user,
          ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function update(Request $request, $id) {
        $user = User::find($id);

        if (!$user || (JWTAuth::parseToken()->authenticate()->id !== $user->id)) {
          return Response::json([
            'error' => [
              'message' => 'You do not have permissions for this user',
            ],
          ], 403, [], JSON_UNESCAPED_UNICODE);
        }

        if ($request->input('password')) {
          $this->validate($request, [
            'password' => 'min:8|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'password_confirmation' => 'min:8|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
          ]);

          if ($request->input('password') !== $request->input('password_confirmation')) {
            return Response::json([
              'error' => [
                'fields' => [
                  'password' => 'The new password does not match the confirm password',
                ]
              ],
            ], 422, [], JSON_UNESCAPED_UNICODE);
          }
        }

        $this->validate($request, [
          'login' => 'max:255|min:8',
          'name' => 'required|min:4|max:255',
          'description' => 'max:255',
          'logo' => 'max:255',
        ]);

        if (!$request->input('country_id') || !Country::find($request->input('country_id'))) {
          return Response::json([
            'error' => [
              'message' => 'The country with id ' . $request->input('country_id') . ' not found',
            ],
          ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if (!$request->input('plan_id') || !Plan::find($request->input('plan_id'))) {
          return Response::json([
            'error' => [
              'message' => 'The plan with id ' . $request->input('country_id') . ' not found',
            ],
          ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $user->fill(array_filter($request->only('country_id', 'plan_id', 'login', 'name', 'description', 'logo'), 'strlen'))->save();

        return Response::json([
          'response' => [
            'user' => $user
          ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function destroy($id) {
        $user = User::find($id);

        $user->delete();
    }
}
