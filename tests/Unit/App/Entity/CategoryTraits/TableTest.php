<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:05 PM
 */

namespace Tests\Unit\App\Entity\CategoryTraits;

use App\Entity\Categories\Table;
use App\Exceptions\ValueNotValid;
use Tests\Helpers\UnitTestCase;

/**
 * Class TableTest
 * @package Tests\Unit\App\Entity\CategoryTraits
 */
class TableTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \App\Entity\CategoryTraits\Table::getTable
   * @covers \App\Entity\CategoryTraits\Table::setTable
   * @uses   \App\Helpers\BasicEnum
   */
  public function testProperty()
  {
    $mock = $this->mock();
    self::assertNull($mock->getTable());
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setTable(Table::ROBERTO_SPORT);
    self::assertEquals(Table::ROBERTO_SPORT, $mock->getTable());
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setTable(null);
    self::assertNull($mock->getTable());
  }

  /**
   * @covers \App\Entity\CategoryTraits\Table::setTable
   * @uses   \App\Helpers\BasicEnum
   * @uses   \App\Exceptions\ValueNotValid::__construct
   */
  public function testPropertyException()
  {
    $mock = $this->mock();
    $this->expectException(ValueNotValid::class);
    $this->expectExceptionMessage(
      'The following value is not valid: 100 in App\Entity\Categories\Table. Possible values: 0, 1, 2, 3, 4, 5.');
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setTable(100);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return \PHPUnit_Framework_MockObject_MockObject|\App\Entity\CategoryTraits\Table
   */
  private function mock(): \PHPUnit_Framework_MockObject_MockObject
  {
    return $this->getMockForTrait(\App\Entity\CategoryTraits\Table::class);
  }
//</editor-fold desc="Private Methods">
}