<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:52 PM
 */

namespace Tests\Unit\App\Entity\Helpers;

use App\Entity\Helpers\BaseEntity;
use Tests\Helpers\UnitTestCase;

/**
 * Class BaseEntityTest
 * @package Tests\Unit\App\Entity\Helpers
 */
class BaseEntityTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Helpers\BaseEntity::methodExists
   */
  public function testMethodExists()
  {
    /** @var BaseEntity $entity */
    $entity = $this->getMockForAbstractClass(BaseEntity::class);

    self::assertTrue($entity->methodExists("methodExists"));
    self::assertFalse($entity->methodExists("notExistingMethod"));
  }
//</editor-fold desc="Public Methods">
}