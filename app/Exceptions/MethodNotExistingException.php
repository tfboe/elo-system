<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 12:43 PM
 */

namespace App\Exceptions;

/**
 * Class MethodNotExistingException
 * @package App\Exceptions
 */
class MethodNotExistingException extends AbstractException
{
//<editor-fold desc="Fields">
  /** @var  string */
  private $methodName;

  /** @var  string */
  private $className;
//</editor-fold desc="Fields">
//<editor-fold desc="Constructor">

  /**
   * MethodNotExistingException constructor.
   * @param string $className
   * @param string $methodName
   */
  public function __construct(string $className, string $methodName)
  {
    $this->className = $className;
    $this->methodName = $methodName;
    parent::__construct("An object of the class $className had no method $methodName");
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
      'message' => "Missing method in object",
      'className' => $this->className,
      'methodName' => $this->methodName
    ];
  }
//</editor-fold desc="Public Methods">
}