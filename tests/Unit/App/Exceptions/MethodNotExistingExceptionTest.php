<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tests\Unit\App\Exceptions;


use App\Exceptions\MethodNotExistingException;
use Tests\Helpers\UnitTestCase;

/**
 * Class AuthenticationExceptionTest
 * @package Tests\Unit\App\Exceptions
 */
class MethodNotExistingExceptionTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Exceptions\MethodNotExistingException::__construct
   */
  public function testConstructor()
  {
    $exc = new MethodNotExistingException("class", "method");
    self::assertEquals($exc->getMessage(), "An object of the class class had no method method");
    self::assertEquals(0, $exc->getCode());
  }

  /**
   * @covers \App\Exceptions\MethodNotExistingException::getJsonMessage
   * @uses   \App\Exceptions\MethodNotExistingException::__construct
   */
  public function testJsonMessage()
  {
    $exc = new MethodNotExistingException("class", "method");
    self::assertEquals(['message' => 'Missing method in object', 'className' => 'class', 'methodName' => 'method'],
      $exc->getJsonMessage());
  }
//</editor-fold desc="Public Methods">
}