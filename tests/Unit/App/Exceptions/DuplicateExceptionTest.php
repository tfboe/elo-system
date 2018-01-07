<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tests\Unit\App\Exceptions;


use App\Exceptions\DuplicateException;
use Tests\Helpers\UnitTestCase;

/**
 * Class ValueNotValidTest
 * @package Tests\Unit\App\Exceptions
 */
class DuplicateExceptionTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Exceptions\DuplicateException::__construct
   */
  public function testConstructor()
  {
    $exc = new DuplicateException('value', 'name', 'array');
    self::assertEquals($exc->getMessage(), "The name value occurs twice in array");
    self::assertEquals(409, $exc->getCode());
  }

  /**
   * @covers \App\Exceptions\DuplicateException::getJsonMessage
   * @uses   \App\Exceptions\DuplicateException::__construct
   */
  public function testJsonMessage()
  {
    $exc = new DuplicateException('value', 'name', 'array');
    self::assertEquals(['message' => 'Duplicate Exception', 'duplicateValue' => 'value', 'arrayName' => 'array'],
      $exc->getJsonMessage());
  }
//</editor-fold desc="Public Methods">
}