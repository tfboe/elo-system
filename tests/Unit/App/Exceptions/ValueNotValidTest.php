<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tests\Unit\App\Exceptions;


use App\Exceptions\ValueNotValid;
use Tests\Helpers\TestCase;
use Tests\Helpers\TestEnum;

/**
 * Class ValueNotValidTest
 * @package Tests\Unit\App\Exceptions
 */
class ValueNotValidTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testConstructor()
  {
    $exc = new ValueNotValid("value");
    self::assertEquals('The following value is not valid: "value"', $exc->getMessage());

    $exc = new ValueNotValid("value", TestEnum::class);
    self::assertEquals('The following value is not valid: "value" in Tests\Helpers\TestEnum. Possible values:' .
      ' "value", 1.', $exc->getMessage());
  }
//</editor-fold desc="Public Methods">
}