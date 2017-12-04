<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/20/17
 * Time: 3:16 PM
 */

namespace App\Exceptions;

/**
 * Class DuplicateException
 * @package App\Exceptions
 */
class UnorderedPhaseNumberException extends AbstractException
{
//<editor-fold desc="Fields">
  /** @var int */
  private $previousPhaseNumber;
  /** @var int */
  private $nextPhaseNumber;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * UnorderedPhaseNumberException constructor.
   * @param int $previousPhaseNumber
   * @param int $nextPhaseNumber
   */
  public function __construct(int $previousPhaseNumber, int $nextPhaseNumber)
  {
    $this->previousPhaseNumber = $previousPhaseNumber;
    $this->nextPhaseNumber = $nextPhaseNumber;

    $message = "The previous phase with number $previousPhaseNumber has a higher phase number than the next phase ";
    $message .= "with number $nextPhaseNumber";
    parent::__construct($message, 409);
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * Gets a json representation of the exception
   * @return array
   */
  public function getJsonMessage()
  {
    return [
      'message' => "Unordered Phase Number Exception",
      'previousPhaseNumber' => $this->previousPhaseNumber,
      'nextPhaseNumber' => $this->nextPhaseNumber
    ];
  }
//</editor-fold desc="Public Methods">
}