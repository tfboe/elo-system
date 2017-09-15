<?php

namespace App\Http\Middleware;

use App\Exceptions\AuthenticationException;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
//<editor-fold desc="Fields">
  /**
   * The authentication guard factory instance.
   *
   * @var \Illuminate\Contracts\Auth\Factory
   */
  protected $auth;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * Create a new middleware instance.
   *
   * @param  \Illuminate\Contracts\Auth\Factory $auth
   */
  public function __construct(Auth $auth)
  {
    $this->auth = $auth;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request $request
   * @param  \Closure $next
   * @param  string|null $guard
   * @return mixed
   * @throws AuthenticationException if request doesn't provide valid authentication token
   */
  public function handle($request, Closure $next, $guard = null)
  {
    if ($this->auth->guard($guard)->guest()) {
      throw new AuthenticationException("Not logged in!");
    }

    return $next($request);
  }
//</editor-fold desc="Public Methods">
}
