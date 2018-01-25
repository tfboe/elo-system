<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:05 PM
 */

namespace Tests\Tfboe\FmLib\Entity\CategoryTraits;

use Tfboe\FmLib\Entity\Categories\OrganizingMode;
use Tfboe\FmLib\Exceptions\ValueNotValid;
use Tests\Helpers\UnitTestCase;

/**
 * Class OrganizingModeTest
 * @package Tests\Tfboe\FmLib\Entity\CategoryTraits
 */
class OrganizingModeTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\OrganizingMode::getOrganizingMode
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\OrganizingMode::setOrganizingMode
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   */
  public function testProperty()
  {
    $mock = $this->mock();
    self::assertNull($mock->getOrganizingMode());
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setOrganizingMode(OrganizingMode::ELIMINATION);
    self::assertEquals(OrganizingMode::ELIMINATION, $mock->getOrganizingMode());
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setOrganizingMode(null);
    self::assertNull($mock->getOrganizingMode());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\OrganizingMode::setOrganizingMode
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   * @uses   \Tfboe\FmLib\Exceptions\ValueNotValid::__construct
   */
  public function testPropertyException()
  {
    $mock = $this->mock();
    $this->expectException(ValueNotValid::class);
    $this->expectExceptionMessage(
      'The following value is not valid: 100 in Tfboe\FmLib\Entity\Categories\OrganizingMode. Possible values: 0, 1.');
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setOrganizingMode(100);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return \PHPUnit_Framework_MockObject_MockObject|\Tfboe\FmLib\Entity\CategoryTraits\OrganizingMode
   */
  private function mock(): \PHPUnit_Framework_MockObject_MockObject
  {
    return $this->getMockForTrait(\Tfboe\FmLib\Entity\CategoryTraits\OrganizingMode::class);
  }
//</editor-fold desc="Private Methods">
}