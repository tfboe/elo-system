<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/10/18
 * Time: 11:10 PM
 */

namespace App\Exceptions;


use Tfboe\FmLib\Exceptions\AbstractException;

/**
 * Class GameHasMissingModes
 * @package App\Exceptions
 */
class GameHasMissingModes extends AbstractException
{
//<editor-fold desc="Constructor">
  /**
   * @inheritDoc
   */
  public function __construct(string $message = "")
  {
    parent::__construct($message, 504);
  }
//</editor-fold desc="Constructor">
}