<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 11:03 AM
 */

namespace Tests\Unit\App\Entity\Categories;

use App\Exceptions\ValueNotValid;
use Tests\Helpers\TestCase;
use Tests\Helpers\TestEnum;

/**
 * Class BasicEnumTest
 * @package Tests\Unit\App\Entity\Categories
 */
class BasicEnumTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testEnsureValidValueException()
  {
    $this->expectException(ValueNotValid::class);
    $this->expectExceptionMessage(
      'The following value is not valid: "1" in Tests\Helpers\TestEnum. Possible values: "value", 1.');
    TestEnum::ensureValidValue('1');
  }

  public function testGetValues()
  {
    self::assertEquals(['value', 1], TestEnum::getValues());
  }

  public function testIsValidName()
  {
    self::assertTrue(TestEnum::isValidName('KEY'));
    self::assertTrue(TestEnum::isValidName('int_key'));
    self::assertFalse(TestEnum::isValidName('INT-KEY'));

    self::assertTrue(TestEnum::isValidName('INT_KEY', True));
    self::assertFalse(TestEnum::isValidName('int_key', True));
  }

  public function testIsValidValue()
  {
    self::assertTrue(TestEnum::isValidValue('value'));
    self::assertTrue(TestEnum::isValidValue(1));
    self::assertFalse(TestEnum::isValidValue('1'));
    self::assertFalse(TestEnum::isValidValue('VALUE'));

    self::assertTrue(TestEnum::isValidValue('1', False));
    self::assertFalse(TestEnum::isValidValue('VALUE', False));
  }

  public function testGetNames()
  {
    self::assertEquals(['KEY', 'INT_KEY'], TestEnum::getNames());
  }

  public function testGetValue()
  {
    self::assertEquals("value", TestEnum::getValue('KEY'));
    self::assertEquals(1, TestEnum::getValue('int_key'));
    self::assertEquals(1, TestEnum::getValue('INT_KEY', True));
  }

  public function testGetValueException()
  {
    $this->expectException(ValueNotValid::class);
    $this->expectExceptionMessage('The following value is not valid: "int_key" in Tests\Helpers\TestEnum. Possible ' .
      'values: "value", 1.');
    TestEnum::getValue('int_key', True);
  }
//</editor-fold desc="Public Methods">
}