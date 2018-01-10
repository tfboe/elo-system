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
use App\Exceptions\ValueNotValid;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Helpers\UnitTestCase;

/**
 * Class BaseEntityTest
 * @package Tests\Unit\App\Entity\Helpers
 */
class ResultEntityTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Helpers\ResultEntity::setPlayed
   * @covers \App\Entity\Helpers\ResultEntity::isPlayed
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   */
  public function testPlayed()
  {
    $entity = $this->mock();
    $played = true;
    $entity->setPlayed($played);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($played, $entity->isPlayed());
  }

  /**
   * @covers \App\Entity\Helpers\ResultEntity::setResult
   * @covers \App\Entity\Helpers\ResultEntity::getResult
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Helpers\BasicEnum
   */
  public function testResult()
  {
    $mock = $this->mock();
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setResult(Result::DRAW);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(Result::DRAW, $mock->getResult());
  }

  /**
   * @covers \App\Entity\Helpers\ResultEntity::setResultA
   * @covers \App\Entity\Helpers\ResultEntity::getResultA
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   */
  public function testResultA()
  {
    $entity = $this->mock();
    $res = 1;
    $entity->setResultA($res);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($res, $entity->getResultA());
  }

  /**
   * @covers \App\Entity\Helpers\ResultEntity::setResultB
   * @covers \App\Entity\Helpers\ResultEntity::getResultB
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   */
  public function testResultB()
  {
    $entity = $this->mock();
    $res = 1;
    $entity->setResultB($res);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($res, $entity->getResultB());
  }

  /**
   * @covers \App\Entity\Helpers\ResultEntity::setResult
   * @uses   \App\Exceptions\ValueNotValid::__construct
   * @uses   \App\Helpers\BasicEnum
   */
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
   * @return MockObject|ResultEntity
   */
  private function mock(): MockObject
  {
    return $this->getMockForTrait(ResultEntity::class, [], '', true, true, true, ['ensureNotNull']);
  }
//</editor-fold desc="Private Methods">
}