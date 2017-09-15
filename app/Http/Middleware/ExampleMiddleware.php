<?php

namespace App\Http\Middleware;

use Closure;

class ExampleMiddleware
{
//<editor-fold desc="Public Methods">
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request $request
   * @param  \Closure $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    return $next($request);
  }
//</editor-fold desc="Public Methods">
}
