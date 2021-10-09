<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/10/18
 * Time: 5:19 PM
 */

namespace App\Http\Controllers;

use App\Entity\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Application;
use Tfboe\FmLib\Entity\UserInterface;
use Tfboe\FmLib\Helpers\Logging;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends \Tfboe\FmLib\Http\Controllers\UserController
{
  /** @noinspection PhpMissingParentCallCommonInspection */
//<editor-fold desc="Protected Methods">
  /**
   * @inheritDoc
   */
  protected function getRegisterResponse(/** @noinspection PhpUnusedParameterInspection */
    Request $request, /** @noinspection PhpUnusedParameterInspection */
    Application $app, UserInterface $user)
  {
    /** @noinspection PhpUnhandledExceptionInspection */
    return $this->login($request, $app);
  }

  public function isAdmin(Request $request)
  {
    $user = $request->user();
    return response()->json(["isAdmin" => $user->isAdmin()], 200);
  }
//</editor-fold desc="Protected Methods">
}