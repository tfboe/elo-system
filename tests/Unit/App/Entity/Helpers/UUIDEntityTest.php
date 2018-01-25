<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 10:39 AM
 */

namespace Tests\Tfboe\FmLib\Entity\Helpers;


use Tfboe\FmLib\Entity\Helpers\UUIDEntity;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Helpers\UnitTestCase;


/**
 * Class BaseEntityTest
 * @package Tests\Tfboe\FmLib\Entity\Helpers
 */
class UUIDEntityTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\UUIDEntity::getId
   * @uses   \Tfboe\FmLib\Entity\Helpers\IdGenerator::createIdFrom
   */
  public function testId()
  {
    $entity = $this->mock();
    /** @noinspection PhpUnhandledExceptionInspection */
    self::getProperty(get_class($entity), 'id')->setValue($entity, 'test-id');
    self::assertEquals('test-id', $entity->getId());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|UUIDEntity
   */
  private function mock(): MockObject
  {
    return $this->getMockForTrait(UUIDEntity::class);
  }
//</editor-fold desc="Private Methods">
}