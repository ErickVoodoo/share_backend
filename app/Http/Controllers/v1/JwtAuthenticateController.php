<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Role;
use App\Permission;
use App\PasswordReset;
use App\User;
use Response;
use JWTAuth;
use Auth;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class JwtAuthenticateController extends App_Controller
{
  public function __construct()
  {
     $this->middleware('jwt.auth', ['except' => ['login', 'registration', 'reset', 'forgot']]);
  }
// LOGIN USER
  public function login(Request $request) {
      $credentials = $request->only('email', 'password');

      try {
          // verify the credentials and create a token for the user
          if (!$token = JWTAuth::attempt($credentials)) {
            return Response::json([
              'error' => [
                'message' => 'Provide credenticals'
              ],
            ], 401, [], JSON_UNESCAPED_UNICODE);
          }
      } catch (JWTException $e) {
          return Response::json([
            'error' => [
              'message' => 'Ooops. Something goes wrong.'
            ],
          ], 500, [], JSON_UNESCAPED_UNICODE);
      }
      $token = JWTAuth::fromUser(Auth::user());
      // if no errors are encountered we can return a JWT
      return Response::json([
        'response' => [
          'token' => $token,
          'user' => Auth::user(),
        ],
      ], 200, [], JSON_UNESCAPED_UNICODE);
  }
// REGISTER USER
  public function registration(Request $request) {
    $this->validate($request, [
      'login' => 'max:255|min:8',
      'email' => 'required|max:255|email',
      'password' => 'min:8|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
      'password_confirmation' => 'min:8|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
      'country_id' => 'required|max:8|numeric',
      'name' => 'required|min:8|max:255',
    ]);

    $login = $request->input('login');
    $email = $request->input('email');
    $password = $request->input('wpassword');
    $country_id = $request->input('country');
    $name = $request->input('name');

    if ($request->input('password') &&
      $request->input('password') !== $request->input('password_confirmation')) {
      return Response::json([
        'error' => [
          'fields' => [
            'passwort' => 'The new password does not match the confirm password',
          ]
        ],
      ], 422, [], JSON_UNESCAPED_UNICODE);
    }

    if(User::where('email', $email)->orWhere('login', $login)->first()) {
      return Response::json([
        'error' => [
          'fields' => [
            'email' => 'The user with this email or login do not exist',
          ],
        ],
      ], 422, [], JSON_UNESCAPED_UNICODE);
    }

    try {
         $user = User::create([
             'login' => $login,
             'email' => $email,
             'password' => bcrypt($password),
             'country_id' => $country_id,
             'name' => $name,
             'plan_id' => 0,
         ]);
     } catch (Exception $e) {
         return Response::json(['error' => 'User already exists.'], HttpResponse::HTTP_CONFLICT);
     }

     $token = JWTAuth::fromUser($user);
     return Response::json([
       'response' => [
         'token' => $token,
         'user' => $user,
       ],
     ], 200, [], JSON_UNESCAPED_UNICODE);
  }
// RESET PASSWORD
  public function reset(Request $request) {
    $email = PasswordReset::where('token', $request->input('token'))->first();

    if (!$email) {
      return Response::json([
        'error' => [
          'fields' => [
            'token' => 'Can\'t find email with this token',
          ]
        ],
      ], 422, [], JSON_UNESCAPED_UNICODE);
    }

    if (!$request->input('password') ||
      $request->input('password') !== $request->input('password_confirmation')) {
      return Response::json([
        'error' => [
          'fields' => [
            'password' => 'The new password does not match the confirm password',
          ]
        ],
      ], 422, [], JSON_UNESCAPED_UNICODE);
    }

    $this->validate($request, [
       'token' => 'required',
       'password' => 'min:8|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
       'password_confirmation' => 'min:8|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
    ]);

    $credentials = $request->only(
      'password', 'password_confirmation', 'token'
    );

    $credentials = array_merge($credentials, ['email' => $email->email]);

    $response = Password::reset($credentials, function ($user, $password) {
      $user->password = bcrypt($password);
      $user->save();
      Auth::login($user);
    });

    switch ($response) {
      case Password::PASSWORD_RESET:
        return Response::json([
          'response' => [
            'message' => trans($response),
          ],
        ], 200, [], JSON_UNESCAPED_UNICODE);

      default:
        return Response::json([
          'error' => [
            'message' => 'Something goes wrong',
          ],
        ], 500, [], JSON_UNESCAPED_UNICODE);
      }
  }
// FORGOT PASSWORD
  public function forgot(Request $request) {
    $this->validate($request, [
      'email' => 'required|email',
    ]);

    if (!User::where('email', $request->input('email'))->first()) {
      return Response::json([
        'error' => [
          'fields' => [
            'email' => 'The user with this email or login do not exist',
          ],
        ],
      ], 422, [], JSON_UNESCAPED_UNICODE);
    }

    $response = Password::sendResetLink($request->only('email'), function (Message $message) {
      $message->subject(isset($this->subject) ? $this->subject : 'Your Password Reset Link');
    });

    switch ($response) {
      case Password::RESET_LINK_SENT:
        $message = 'Reset password was sent';

      case Password::INVALID_USER:
        $message = 'Invalid user';
    }

    return Response::json([
      'response' => [
        'message' => $message,
        'data' => trans($response),
      ],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  public function createRole(Request $request) {
    $name = $request->input('name');
    if (!$name) {
      return Response::json([
        'response' => [
          'fields' => [
            'name' => 'Provide role name',
          ],
        ],
      ], 422, [], JSON_UNESCAPED_UNICODE);
    }

    $role = Role::create(array(
      'name' => $name,
    ));

    return Response::json([
      'response' => [
        'role' => $role,
      ],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  public function createPermission(Request $request) {
    $name = $request->input('name');
    if (!$name) {
      return Response::json([
        'response' => [
          'fields' => [
            'name' => 'Provide permission name',
          ],
        ],
      ], 422, [], JSON_UNESCAPED_UNICODE);
    }

    $permission = Permission::create([
      'name' => $name,
    ]);

    return Response::json([
      'response' => [
        'permission' => $permission,
      ],
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }

  public function assignRole(Request $request) {
    $user_email = $request->input('email');
    $role_name = $request->input('role');

    $user = User::where('email', $user_email)->first();
    $role = Role::where('name', $role_name)->first();

    if (!$user_email || !$user) {
      return Response::json([
        'response' => [
          'fields' => [
            'email' => 'Provide user email',
          ],
        ],
      ], 422, [], JSON_UNESCAPED_UNICODE);
    }

    if (!$role_name || !$role) {
      return Response::json([
        'response' => [
          'fields' => [
            'role' => 'Provide role name',
          ],
        ],
      ], 422, [], JSON_UNESCAPED_UNICODE);
    }

    $user->roles()->attach($role->id);

    return Response::json([
      'response' => '',
    ], 201, [], JSON_UNESCAPED_UNICODE);
  }

  public function attachPermission(Request $request) {
    $role_name = $request->input('role');
    $role = Role::where('name', $role_name)->first();

    $permission_name = $request->input('permission');
    $permission = Permission::where('name', $permission_name)->first();

    if (!$role_name || !$role) {
      return Response::json([
        'response' => [
          'fields' => [
            'role' => 'Provide role name',
          ],
        ],
      ], 422, [], JSON_UNESCAPED_UNICODE);
    }

    if (!$permission_name || !$permission) {
      return Response::json([
        'response' => [
          'fields' => [
            'permission' => 'Provide permission name',
          ],
        ],
      ], 422, [], JSON_UNESCAPED_UNICODE);
    }

    $role->attachPermission($permission);

    return Response::json([
      'response' => '',
    ], 201, [], JSON_UNESCAPED_UNICODE);
  }
}
