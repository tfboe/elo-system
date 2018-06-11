<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 12/30/16
 * Time: 12:19 PM
 */

namespace App\Http\Middleware;

use Illuminate\Http\Request;


//TODO remove this class when bug #291 (https://github.com/laravel/lumen-framework/issues/291) is fixed

/**
 * Class Authorize
 * @package App\Http\Middleware
 */
class Authorize extends \Illuminate\Auth\Middleware\Authorize
{
  /** @noinspection PhpMissingParentCallCommonInspection */
//<editor-fold desc="Protected Methods">
  /**
   * Overwrites the getModel function by using the correct access to request->route()
   * @param Request $request
   * @param string $model
   * @return string
   */
  protected function getModel($request, $model)
  {
    return $this->isClassName($model) ? $model : $request->route()[2][$model];
  }
//</editor-fold desc="Protected Methods">
}