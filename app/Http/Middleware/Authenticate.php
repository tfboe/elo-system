<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Entity\User;
use App\Exceptions\AuthenticationException;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Tymon\JWTAuth\Payload;

/**
 * Class Authenticate
 * @package App\Http\Middleware
 */
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
   * @param  string|null $guardName
   * @return mixed
   * @throws AuthenticationException if request doesn't provide valid authentication token
   */
  public function handle($request, Closure $next, $guardName = null)
  {
    $guard = $this->auth->guard($guardName);
    if ($guard->guest()) {
      throw new AuthenticationException("Not logged in!");
    }
    /** @var Payload $payload */
    $payload = $guard->getPayload();
    /** @var User $user */
    $user = $guard->getUser();
    if (!$payload->hasKey('ver') || !($user instanceof User) || $payload->get(['ver'])[0] <
      $user->getJwtVersion()) {
      throw new AuthenticationException("Payload version expired!");
    }

    return $next($request);
  }
//</editor-fold desc="Public Methods">
}
