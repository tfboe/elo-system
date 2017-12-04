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
class ReferenceException extends AbstractException
{
//<editor-fold desc="Fields">
  /** @var mixed */
  private $referenceValue;
  /** @var string */
  private $referenceName;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * ReferenceException constructor.
   * @param $referenceValue
   * @param string $referenceName
   */
  public function __construct($referenceValue, string $referenceName)
  {
    $this->referenceName = $referenceName;
    $this->referenceValue = $referenceValue;

    $message = "The reference $referenceValue of $referenceName is not existing!";
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
      'message' => "Reference Exception",
      'referenceValue' => $this->referenceValue,
      'referenceName' => $this->referenceName
    ];
  }
//</editor-fold desc="Public Methods">
}