<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:05 PM
 */

namespace Tests\Unit\App\Entity\CategoryTraits;

use App\Entity\Categories\OrganizingMode;
use App\Exceptions\ValueNotValid;
use Tests\Helpers\TestCase;

/**
 * Class OrganizingModeTest
 * @package Tests\Unit\App\Entity\CategoryTraits
 */
class OrganizingModeTest extends TestCase
{
//<editor-fold desc="Public Methods">
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

  public function testPropertyException()
  {
    $mock = $this->mock();
    $this->expectException(ValueNotValid::class);
    $this->expectExceptionMessage(
      'The following value is not valid: 100 in App\Entity\Categories\OrganizingMode. Possible values: 0, 1.');
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setOrganizingMode(100);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return \PHPUnit_Framework_MockObject_MockObject|\App\Entity\CategoryTraits\OrganizingMode
   */
  private function mock(): \PHPUnit_Framework_MockObject_MockObject
  {
    return $this->getMockForTrait(\App\Entity\CategoryTraits\OrganizingMode::class);
  }
//</editor-fold desc="Private Methods">
}