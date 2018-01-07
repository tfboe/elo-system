<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tests\Unit\App\Exceptions;


use App\Exceptions\ReferenceException;
use Tests\Helpers\UnitTestCase;

/**
 * Class ValueNotValidTest
 * @package Tests\Unit\App\Exceptions
 */
class ReferenceExceptionTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Exceptions\ReferenceException::__construct
   */
  public function testConstructor()
  {
    $exc = new ReferenceException('value', 'name');
    self::assertEquals("The reference value of name is not existing!", $exc->getMessage());
    self::assertEquals(409, $exc->getCode());
  }

  /**
   * @covers \App\Exceptions\ReferenceException::getJsonMessage
   * @uses   \App\Exceptions\ReferenceException::__construct
   */
  public function testJsonMessage()
  {
    $exc = new ReferenceException('value', 'name');
    self::assertEquals(['message' => 'Reference Exception', 'referenceValue' => 'value', 'referenceName' => 'name'],
      $exc->getJsonMessage());
  }
//</editor-fold desc="Public Methods">
}