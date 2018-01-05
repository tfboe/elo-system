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
class PropertyNotExistingException extends AbstractException
{
//<editor-fold desc="Fields">
  /** @var string */
  private $propertyName;

  /** @var string */
  private $className;

  /** @var string */
  private $accessorMethod;
//</editor-fold desc="Fields">
//<editor-fold desc="Constructor">


  /**
   * PropertyNotExistingException constructor.
   * @param string $className
   * @param string $propertyName
   * @param string $accessorMethod
   */
  public function __construct(string $className, string $propertyName, string $accessorMethod)
  {
    $this->className = $className;
    $this->propertyName = $propertyName;
    $this->accessorMethod = $accessorMethod;
    parent::__construct("An object of the class $className had no property $propertyName via $accessorMethod");
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
      'message' => "Missing property in object",
      'className' => $this->className,
      'propertyName' => $this->propertyName,
      'accessorMethod' => $this->accessorMethod
    ];
  }
//</editor-fold desc="Public Methods">
}