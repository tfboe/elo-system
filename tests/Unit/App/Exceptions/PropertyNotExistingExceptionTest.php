<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tests\Unit\App\Exceptions;


use App\Exceptions\PropertyNotExistingException;
use Tests\Helpers\UnitTestCase;

/**
 * Class AuthenticationExceptionTest
 * @package Tests\Unit\App\Exceptions
 */
class PropertyNotExistingExceptionTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Exceptions\PropertyNotExistingException::__construct
   */
  public function testConstructor()
  {
    $exc = new PropertyNotExistingException("class", "property", "getProperty");
    self::assertEquals($exc->getMessage(), "An object of the class class had no property property via getProperty");
    self::assertEquals(0, $exc->getCode());
  }

  /**
   * @covers \App\Exceptions\PropertyNotExistingException::getJsonMessage
   * @uses   \App\Exceptions\PropertyNotExistingException::__construct
   */
  public function testJsonMessage()
  {
    $exc = new PropertyNotExistingException("class", "property", "getProperty");
    self::assertEquals(['message' => 'Missing property in object', 'className' => 'class', 'propertyName' => 'property',
      'accessorMethod' => 'getProperty'], $exc->getJsonMessage());
  }
//</editor-fold desc="Public Methods">
}