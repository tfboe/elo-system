<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/15/17
 * Time: 11:18 PM
 */

namespace App\Exceptions;

/**
 * Class AuthenticationException
 * @package App\Exceptions
 */
class ValueNotSet extends AbstractException
{
//<editor-fold desc="Constructor">
  /**
   * ValueNotSet constructor.
   * @param string $class the class the property belongs to
   * @param string $property the not set property which got accessed
   */
  public function __construct(string $class, string $property)
  {
    $message = "The property " . $property . " of the class " . $class . " must be set before it can be accessed." .
      " Please set the property immediately after you call the constructor(Empty Constructor Pattern).";
    parent::__construct($message);
  }
//</editor-fold desc="Constructor">
}