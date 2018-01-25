<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tests\Tfboe\FmLib\Exceptions;


use Tfboe\FmLib\Exceptions\PropertyNotExistingException;
use Tests\Helpers\UnitTestCase;

/**
 * Class AuthenticationExceptionTest
 * @package Tests\Tfboe\FmLib\Exceptions
 */
class PropertyNotExistingExceptionTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Exceptions\PropertyNotExistingException::__construct
   */
  public function testConstructor()
  {
    $exc = new PropertyNotExistingException("class", "property", "getProperty");
    self::assertEquals($exc->getMessage(), "An object of the class class had no property property via getProperty");
    self::assertEquals(0, $exc->getCode());
  }

  /**
   * @covers \Tfboe\FmLib\Exceptions\PropertyNotExistingException::getJsonMessage
   * @uses   \Tfboe\FmLib\Exceptions\PropertyNotExistingException::__construct
   */
  public function testJsonMessage()
  {
    $exc = new PropertyNotExistingException("class", "property", "getProperty");
    self::assertEquals(['message' => 'Missing property in object', 'className' => 'class', 'propertyName' => 'property',
      'accessorMethod' => 'getProperty'], $exc->getJsonMessage());
  }
//</editor-fold desc="Public Methods">
}