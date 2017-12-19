<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:52 PM
 */

namespace Tests\Unit\App\Entity\Helpers;

use App\Entity\Helpers\Result;
use App\Entity\Helpers\ResultEntity;
use App\Exceptions\ValueNotSet;
use App\Exceptions\ValueNotValid;
use Tests\Helpers\TestCase;

/**
 * Class BaseEntityTest
 * @package Tests\Unit\App\Entity\Helpers
 */
class ResultEntityTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testPlayed()
  {
    $e = $this->mock();
    $played = true;
    $e->setPlayed($played);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($played, $e->isPlayed());
  }

  public function testPlayedException()
  {
    $e = $this->mock();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property played of the class " . get_class($e) . " must be set before " .
      "it can be accessed. Please set the property immediately after you call the " .
      "constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->isPlayed();
  }

  public function testResult()
  {
    $mock = $this->mock();
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setResult(Result::DRAW);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(Result::DRAW, $mock->getResult());
  }

  public function testResultA()
  {
    $e = $this->mock();
    $res = 1;
    $e->setResultA($res);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($res, $e->getResultA());
  }

  public function testResultAException()
  {
    $e = $this->mock();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property resultA of the class " . get_class($e) . " must be set before " .
      "it can be accessed. Please set the property immediately after you call the " .
      "constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getResultA();
  }

  public function testResultB()
  {
    $e = $this->mock();
    $res = 1;
    $e->setResultB($res);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($res, $e->getResultB());
  }

  public function testResultBException()
  {
    $e = $this->mock();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property resultB of the class " . get_class($e) . " must be set before " .
      "it can be accessed. Please set the property immediately after you call the " .
      "constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getResultB();
  }

  public function testResultNotSetException()
  {
    $e = $this->mock();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property result of the class " . get_class($e) . " must be set before " .
      "it can be accessed. Please set the property immediately after you call the " .
      "constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getResult();
  }

  public function testResultNotValidException()
  {
    $mock = $this->mock();
    $this->expectException(ValueNotValid::class);
    $this->expectExceptionMessage(
      'The following value is not valid: 100 in App\Entity\Helpers\Result. Possible values: 0, 1, 2, 3, 4.');
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setResult(100);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return \PHPUnit_Framework_MockObject_MockObject|ResultEntity
   */
  private function mock(): \PHPUnit_Framework_MockObject_MockObject
  {
    return $this->getMockForTrait(ResultEntity::class);
  }
//</editor-fold desc="Private Methods">
}