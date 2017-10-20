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
class DuplicateException extends AbstractException
{
//<editor-fold desc="Fields">
  /** @var string */
  private $duplicateValue;
  /** @var string */
  private $duplicateName;
  /** @var string */
  private $arrayName;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * DuplicateException constructor.
   * @param mixed $duplicateValue
   * @param string $duplicateName
   * @param string $arrayName
   */
  public function __construct($duplicateValue, string $duplicateName, string $arrayName)
  {
    $this->duplicateValue = $duplicateValue;
    $this->duplicateName = $duplicateName;
    $this->arrayName = $arrayName;

    $message = "The $duplicateName $duplicateValue occurs twice in $arrayName";
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
      'message' => "Duplicate Exception",
      'duplicateValue' => $this->duplicateValue,
      'arrayName' => $this->arrayName
    ];
  }
//</editor-fold desc="Public Methods">
}