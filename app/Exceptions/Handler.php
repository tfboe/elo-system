<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 2/5/18
 * Time: 3:41 PM
 */

namespace App\Exceptions;


use Throwable;

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
  protected function getExceptionName(Throwable $exception): string
  {
    if ($exception instanceof GameHasMissingModes) {
      return ExceptionNames::GAME_HAS_MISSING_MODES;
    }
    if ($exception instanceof ManualValidationException) {
      return ExceptionNames::MANUAL_VALIDAION_EXCEPTION;
    }
    return parent::getExceptionName($exception);
  }
//</editor-fold desc="Protected Methods">
}