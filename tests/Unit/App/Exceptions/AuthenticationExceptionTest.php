<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tests\Unit\App\Exceptions;


use App\Exceptions\AuthenticationException;
use Tests\Helpers\TestCase;

/**
 * Class AuthenticationExceptionTest
 * @package Tests\Unit\App\Exceptions
 */
class AuthenticationExceptionTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testConstructor()
  {
    $exc = new AuthenticationException("message");
    self::assertEquals("message", $exc->getMessage());
    $exc2 = new AuthenticationException("");
    self::assertEquals("", $exc2->getMessage());
  }
//</editor-fold desc="Public Methods">
}