<?php

namespace App\Http\Middleware;
use Auth;
use Response;
use Closure;

class Core {
  public function handle($request, Closure $next) {
    $access_token = $request->header('auth_token');

    // if (!$access_token) {
    //   return Response::json([
    //     'error' => "Provide access token",
    //   ],403);
    // }

    return Auth::onceBasic('login') ?: $next($request);
  }
}
