<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tests\Tfboe\FmLib\Exceptions;


use Tfboe\FmLib\Exceptions\DuplicateException;
use Tests\Helpers\UnitTestCase;

/**
 * Class ValueNotValidTest
 * @package Tests\Tfboe\FmLib\Exceptions
 */
class DuplicateExceptionTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Exceptions\DuplicateException::__construct
   */
  public function testConstructor()
  {
    $exc = new DuplicateException('value', 'name', 'array');
    self::assertEquals($exc->getMessage(), "The name value occurs twice in array");
    self::assertEquals(409, $exc->getCode());
  }

  /**
   * @covers \Tfboe\FmLib\Exceptions\DuplicateException::getJsonMessage
   * @uses   \Tfboe\FmLib\Exceptions\DuplicateException::__construct
   */
  public function testJsonMessage()
  {
    $exc = new DuplicateException('value', 'name', 'array');
    self::assertEquals(['message' => 'Duplicate Exception', 'duplicateValue' => 'value', 'arrayName' => 'array'],
      $exc->getJsonMessage());
  }
//</editor-fold desc="Public Methods">
}