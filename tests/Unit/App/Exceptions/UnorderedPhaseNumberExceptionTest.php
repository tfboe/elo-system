<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tests\Unit\App\Exceptions;

use App\Exceptions\UnorderedPhaseNumberException;
use Tests\Helpers\TestCase;

/**
 * Class ValueNotValidTest
 * @package Tests\Unit\App\Exceptions
 */
class UnorderedPhaseNumberExceptionTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testConstructor()
  {
    $exc = new UnorderedPhaseNumberException(2, 1);
    self::assertEquals("The previous phase with number 2 has a higher phase number than the next phase with " .
      "number 1", $exc->getMessage());
    self::assertEquals(409, $exc->getCode());
  }


  public function testJsonMessage()
  {
    $exc = new UnorderedPhaseNumberException(2, 1);
    self::assertEquals(['message' => 'Unordered Phase Number Exception', 'previousPhaseNumber' => 2,
      'nextPhaseNumber' => 1], $exc->getJsonMessage());
  }
//</editor-fold desc="Public Methods">
}