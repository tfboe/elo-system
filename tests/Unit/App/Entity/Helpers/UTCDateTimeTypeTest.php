<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 9:46 PM
 */

namespace Tests\Unit\App\Entity\Helpers;


use App\Entity\Helpers\UTCDateTimeType;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Tests\Helpers\UnitTestCase;

/**
 * Class UTCDateTimeTypeTest
 * @package Tests\Unit\App\Entity\Helpers
 */
class UTCDateTimeTypeTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Helpers\UTCDateTimeType::convertToDatabaseValue
   * @covers \App\Entity\Helpers\UTCDateTimeType::getUtc
   */
  public function testConvertToDatabaseValue()
  {
    /** @noinspection PhpUnhandledExceptionInspection */
    //$type = Type::getType("datetime");
    /** @var UTCDateTimeType $type */
    $type = $this->getMockForAbstractClass(UTCDateTimeType::class, [], '', false);
    $platform = new MySqlPlatform();
    $datetime = new \DateTime("2017-12-31 15:23:20 +02:00");
    $value = $type->convertToDatabaseValue($datetime, $platform);
    self::assertEquals("2017-12-31 13:23:20", $value);

    $datetime = new \DateTime("2017-12-31 15:23:20");
    $value = $type->convertToDatabaseValue($datetime, $platform);
    self::assertEquals("2017-12-31 15:23:20", $value);
  }
//</editor-fold desc="Public Methods">
}