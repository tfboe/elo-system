<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tests\Tfboe\FmLib\Exceptions;

use Tfboe\FmLib\Exceptions\UnorderedPhaseNumberException;
use Tests\Helpers\UnitTestCase;

/**
 * Class ValueNotValidTest
 * @package Tests\Tfboe\FmLib\Exceptions
 */
class UnorderedPhaseNumberExceptionTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Exceptions\UnorderedPhaseNumberException::__construct
   */
  public function testConstructor()
  {
    $exc = new UnorderedPhaseNumberException(2, 1);
    self::assertEquals("The previous phase with number 2 has a higher phase number than the next phase with " .
      "number 1", $exc->getMessage());
    self::assertEquals(409, $exc->getCode());
  }

  /**
   * @covers \Tfboe\FmLib\Exceptions\UnorderedPhaseNumberException::getJsonMessage
   * @uses   \Tfboe\FmLib\Exceptions\UnorderedPhaseNumberException::__construct
   */
  public function testJsonMessage()
  {
    $exc = new UnorderedPhaseNumberException(2, 1);
    self::assertEquals(['message' => 'Unordered Phase Number Exception', 'previousPhaseNumber' => 2,
      'nextPhaseNumber' => 1], $exc->getJsonMessage());
  }
//</editor-fold desc="Public Methods">
}