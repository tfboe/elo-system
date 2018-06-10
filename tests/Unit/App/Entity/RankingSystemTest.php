<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/10/18
 * Time: 2:22 PM
 */

namespace Tests\Unit\App\Entity;


use Tfboe\FmLib\TestHelpers\UnitTestCase;

/**
 * Class RankingSystem
 * @package Tests\Unit\App\Entity
 */
class RankingSystemTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\RankingSystem::__construct
   */
  public function testConstructor()
  {
    $entity = new \App\Entity\RankingSystem(["subclassData"]);
    self::assertInstanceOf(\App\Entity\RankingSystem::class, $entity);
    self::assertTrue($entity->hasProperty("subClassData"));
    /** @noinspection PhpUndefinedMethodInspection */
    self::assertNull($entity->getSubClassData());
  }
//</editor-fold desc="Public Methods">
}