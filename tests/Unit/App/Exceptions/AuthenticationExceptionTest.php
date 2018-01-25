<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tests\Tfboe\FmLib\Exceptions;


use Tfboe\FmLib\Exceptions\AuthenticationException;
use Tests\Helpers\UnitTestCase;

/**
 * Class AuthenticationExceptionTest
 * @package Tests\Tfboe\FmLib\Exceptions
 */
class AuthenticationExceptionTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Exceptions\AuthenticationException::__construct
   */
  public function testConstructor()
  {
    $prev = new \Exception();
    $exc = new AuthenticationException("message", $prev);
    self::assertEquals("message", $exc->getMessage());
    self::assertEquals($prev, $exc->getPrevious());
    $exc2 = new AuthenticationException("message");
    self::assertEquals("message", $exc2->getMessage());
    $exc3 = new AuthenticationException();
    self::assertEquals("", $exc3->getMessage());
  }
//</editor-fold desc="Public Methods">
}