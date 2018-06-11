<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/10/18
 * Time: 5:19 PM
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Laravel\Lumen\Application;
use Tfboe\FmLib\Entity\UserInterface;

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
//</editor-fold desc="Protected Methods">
}