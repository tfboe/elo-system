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
class ValueNotValid extends AbstractException
{
//<editor-fold desc="Constructor">
  /**
   * @param mixed $value
   * @param string|null $enum_name the corresponding enum class name or null
   * @param string $valueFunction the function to call on the enum class to get the list of possible values
   */
  public function __construct($value, ?string $enum_name = null, string $valueFunction = "getValues")
  {
    $message = "The following value is not valid: " . json_encode($value);
    if ($enum_name != null && method_exists($enum_name, $valueFunction)) {
      /** @var mixed[] $values */
      $values = call_user_func([$enum_name, $valueFunction]);
      assert(!in_array($value, $values, true));
      $message .= " in " . $enum_name . ". Possible values: " .
        implode(', ', array_map("json_encode", $values)) . ".";
    }
    parent::__construct($message);
  }
//</editor-fold desc="Constructor">
}