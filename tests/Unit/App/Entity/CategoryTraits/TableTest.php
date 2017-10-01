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
use Tests\Helpers\TestCase;

class TableTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testProperty()
  {
    $mock = $this->mock();
    self::assertNull($mock->getTable());
    $mock->setTable(Table::ROBERTO_SPORT);
    self::assertEquals(Table::ROBERTO_SPORT, $mock->getTable());
  }

  public function testPropertyException()
  {
    $mock = $this->mock();
    $this->expectException(ValueNotValid::class);
    $this->expectExceptionMessage(
      'The following value is not valid: 100 in App\Entity\Categories\Table. Possible values: 0, 1, 2, 3, 4, 5.');
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