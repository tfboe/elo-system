<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/11/18
 * Time: 11:39 AM
 */

namespace App\Providers;


class CatchAllOptionsRequestsProvider
{
//<editor-fold desc="Public Methods">
  public function register()
  {
    $request = app('request');
    if ($request->isMethod('OPTIONS')) {
      app()->options($request->path(), function () {
        return response('', 200);
      });
    }
  }
//</editor-fold desc="Public Methods">
}