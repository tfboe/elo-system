<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Entity\User;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Tfboe\FmLib\Exceptions\AuthenticationException;

/**
 * Class Authenticate
 * @package App\Http\Middleware
 */
class Admin
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
   * @param  string|null $guardName
   * @return mixed
   * @throws AuthenticationException if request doesn't provide valid authentication token
   */
  public function handle($request, Closure $next, $guardName = null)
  {
    /** @var User $user */
    $user = $this->auth->guard($guardName)->getUser();
    if (!($user instanceof User) || !$user->isAdmin()) {
      throw new AuthenticationException("No admin rights!");
    }

    return $next($request);
  }
//</editor-fold desc="Public Methods">
}
