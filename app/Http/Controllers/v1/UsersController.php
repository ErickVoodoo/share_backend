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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
      $users = new User;

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $user = User::where('id', $id)
          ->with(
            'products.tags',
            'products.links',
            'products.images',
            'products.discount',
            'products.location'
          );

        return Response::json([
          'response' => ['user' => $user->get()],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $user = User::find($id);

        if (!$user || (JWTAuth::parseToken()->authenticate()->id !== $user->id)) {
          return Response::json([
            'error' => [
              'message' => 'You do not have permissions for this user',
            ],
          ], 403, [], JSON_UNESCAPED_UNICODE);
        }

        if (!$request->input('password') ||
          $request->input('password') !== $request->input('password_confirmation')) {
          return Response::json([
            'error' => [
              'fields' => [
                'passwort' => 'The new password does not match the confirm password',
              ]
            ],
          ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $this->validate($request, [
          'login' => 'max:255|min:8',
          'email' => 'required|max:255|email|unique:users',
          'password' => 'min:8|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
          'password_confirmation' => 'min:8|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
          'name' => 'required|min:8|max:255',
          'country_id' => 'required|max:8|numeric',
          'plan_id' => 'max:8|numeric',
          'description' => 'max:255',
          'logo' => 'max:255',
        ]);

        if ($request->input('country_id') || !Country::find($request->input('country_id'))) {
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

        $user->fill($request->all())->save();

        return Response::json([
          'response' => ['user' => $user],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $user = User::find($id);

        $user->delete();
    }
}
