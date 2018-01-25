<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:52 PM
 */

namespace Tests\Tfboe\FmLib\Entity\Helpers;

use Tfboe\FmLib\Entity\Helpers\Result;
use Tfboe\FmLib\Entity\Helpers\ResultEntity;
use Tfboe\FmLib\Exceptions\ValueNotValid;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Helpers\UnitTestCase;

/**
 * Class BaseEntityTest
 * @package Tests\Tfboe\FmLib\Entity\Helpers
 */
class ResultEntityTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::setPlayed
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::isPlayed
   */
  public function testPlayed()
  {
    $entity = $this->mock();
    $played = true;
    $entity->setPlayed($played);
    self::assertEquals($played, $entity->isPlayed());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::setResult
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::getResult
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   */
  public function testResult()
  {
    $mock = $this->mock();
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setResult(Result::DRAW);
    self::assertEquals(Result::DRAW, $mock->getResult());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::setResultA
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::getResultA
   */
  public function testResultA()
  {
    $entity = $this->mock();
    $res = 1;
    $entity->setResultA($res);
    self::assertEquals($res, $entity->getResultA());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::setResultB
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::getResultB
   */
  public function testResultB()
  {
    $entity = $this->mock();
    $res = 1;
    $entity->setResultB($res);
    self::assertEquals($res, $entity->getResultB());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::setResult
   * @uses   \Tfboe\FmLib\Exceptions\ValueNotValid::__construct
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   */
  public function testResultNotValidException()
  {
    $mock = $this->mock();
    $this->expectException(ValueNotValid::class);
    $this->expectExceptionMessage(
      'The following value is not valid: 100 in Tfboe\FmLib\Entity\Helpers\Result. Possible values: 0, 1, 2, 3, 4.');
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
    return $this->getMockForTrait(ResultEntity::class);
  }
//</editor-fold desc="Private Methods">
}