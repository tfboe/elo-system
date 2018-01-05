<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 10:39 AM
 */

namespace Tests\Unit\App\Entity\Helpers;

use App\Entity\Helpers\UUIDEntity;
use App\Entity\User;
use App\Exceptions\ValueNotSet;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\TestCase;

/**
 * Class BaseEntityTest
 * @package Tests\Unit\App\Entity\Helpers
 */
class UUIDEntityTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testId()
  {
    $e = $this->mock();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($e);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $e->getId());
  }

  public function testIdException()
  {
    $e = $this->mock();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property id of the class " . get_class($e) . " must be set before it can" .
      " be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getId();
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return UUIDEntity
   */
  private function mock()
  {
    return new User();
  }
//</editor-fold desc="Private Methods">
}