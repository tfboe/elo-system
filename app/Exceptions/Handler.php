<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 2/5/18
 * Time: 3:41 PM
 */

namespace App\Exceptions;


use Exception;

/**
 * Class Handler
 * @package App\Exceptions
 */
class Handler extends \Tfboe\FmLib\Exceptions\Handler
{
//<editor-fold desc="Protected Methods">
  /**
   * @inheritDoc
   */
  protected function getExceptionName(Exception $exception): string
  {
    if ($exception instanceof GameHasMissingModes) {
      return ExceptionNames::GAME_HAS_MISSING_MODES;
    }
    return parent::getExceptionName($exception);
  }
//</editor-fold desc="Protected Methods">
}