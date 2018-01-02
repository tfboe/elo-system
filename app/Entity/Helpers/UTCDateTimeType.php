<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 9:22 PM
 */

namespace App\Entity\Helpers;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeType;

/**
 * Class UTCDateTimeType
 * @package App\Entity\Helpers
 */
class UTCDateTimeType extends DateTimeType
{
//<editor-fold desc="Fields">
  static private $utc;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * {@inheritdoc}
   */
  public function convertToDatabaseValue($value, AbstractPlatform $platform)
  {
    if ($value instanceof \DateTime) {
      $value = clone $value;
      $value->setTimezone(self::getUtc());
    }

    return parent::convertToDatabaseValue($value, $platform);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * Gets the utc datetime zone
   * @return \DateTimeZone
   */
  private static function getUtc(): \DateTimeZone
  {
    return self::$utc ? self::$utc : self::$utc = new \DateTimeZone('UTC');
  }
//</editor-fold desc="Private Methods">
}