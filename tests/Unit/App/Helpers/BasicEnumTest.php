<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 11:03 AM
 */

namespace Tests\Unit\App\Helpers;

use App\Exceptions\ValueNotValid;
use App\Helpers\BasicEnum;
use Tests\Helpers\TestEnum;
use Tests\Helpers\UnitTestCase;

/**
 * Class BasicEnumTest
 * @package Tests\Unit\App\Helpers
 */
class BasicEnumTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Helpers\BasicEnum::ensureValidValue
   * @uses   \App\Exceptions\ValueNotValid::__construct
   * @uses   \App\Helpers\BasicEnum::getConstants
   * @uses   \App\Helpers\BasicEnum::getValues
   * @uses   \App\Helpers\BasicEnum::isValidValue
   */
  public function testEnsureValidValueException()
  {
    /** @noinspection PhpUnhandledExceptionInspection */
    TestEnum::ensureValidValue(TestEnum::INT_KEY);
    $this->expectException(ValueNotValid::class);
    $this->expectExceptionMessage(
      'The following value is not valid: "1" in Tests\Helpers\TestEnum. Possible values: "value", 1.');
    /** @noinspection PhpUnhandledExceptionInspection */
    TestEnum::ensureValidValue('1');
  }

  /**
   * @covers \App\Helpers\BasicEnum::getNames
   * @covers \App\Helpers\BasicEnum::getConstants
   */
  public function testGetNames()
  {
    self::assertEquals(['KEY', 'INT_KEY'], TestEnum::getNames());
  }

  /**
   * @covers \App\Helpers\BasicEnum::getValue
   * @covers \App\Helpers\BasicEnum::getCaseMapping
   * @uses   \App\Helpers\BasicEnum::getConstants
   * @uses   \App\Helpers\BasicEnum::getNames
   */
  public function testGetValue()
  {
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals("value", TestEnum::getValue('KEY'));
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, TestEnum::getValue('int_key'));
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, TestEnum::getValue('INT_KEY', True));
  }

  /**
   * @covers \App\Helpers\BasicEnum::getValue
   * @uses   \App\Exceptions\ValueNotValid::__construct
   * @uses   \App\Helpers\BasicEnum::getConstants
   * @uses   \App\Helpers\BasicEnum::getValues
   */
  public function testGetValueException()
  {
    $this->expectException(ValueNotValid::class);
    $this->expectExceptionMessage('The following value is not valid: "int_key" in Tests\Helpers\TestEnum. Possible ' .
      'values: "value", 1.');
    /** @noinspection PhpUnhandledExceptionInspection */
    TestEnum::getValue('int_key', True);
  }

  /**
   * @covers \App\Helpers\BasicEnum::getValues
   * @uses   \App\Helpers\BasicEnum::getConstants
   */
  public function testGetValues()
  {
    self::assertEquals(['value', 1], TestEnum::getValues());
  }

  /**
   * @covers \App\Helpers\BasicEnum::isValidName
   * @uses   \App\Helpers\BasicEnum::getConstants
   */
  public function testIsValidName()
  {
    self::assertTrue(TestEnum::isValidName('KEY'));
    self::assertTrue(TestEnum::isValidName('int_key'));
    self::assertFalse(TestEnum::isValidName('INT-KEY'));

    self::assertTrue(TestEnum::isValidName('INT_KEY', True));
    self::assertFalse(TestEnum::isValidName('int_key', True));
  }

  /**
   * @covers \App\Helpers\BasicEnum::isValidValue
   * @uses   \App\Helpers\BasicEnum::getConstants
   * @uses   \App\Helpers\BasicEnum::getValues
   */
  public function testIsValidValue()
  {
    self::assertTrue(TestEnum::isValidValue('value'));
    self::assertTrue(TestEnum::isValidValue(1));
    self::assertFalse(TestEnum::isValidValue('1'));
    self::assertFalse(TestEnum::isValidValue('VALUE'));

    self::assertTrue(TestEnum::isValidValue('1', False));
    self::assertFalse(TestEnum::isValidValue('VALUE', False));
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Methods">
  /**
   * @before
   */
  protected function clearStaticVariables()
  {
    /** @noinspection PhpUnhandledExceptionInspection */
    self::getProperty(BasicEnum::class, 'constCacheArray')->setValue(NULL);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::getProperty(BasicEnum::class, 'constCacheArrayCaseMapping')->setValue(NULL);
  }
//</editor-fold desc="Protected Methods">
}