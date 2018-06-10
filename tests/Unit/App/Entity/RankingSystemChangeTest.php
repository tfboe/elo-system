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
 * Class RankingSystemChange
 * @package Tests\Unit\App\Entity
 */
class RankingSystemChangeTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\RankingSystemChange::__construct
   */
  public function testConstructor()
  {
    $entity = new \App\Entity\RankingSystemChange(["subclassData"]);
    self::assertInstanceOf(\App\Entity\RankingSystemChange::class, $entity);
    self::assertTrue($entity->hasProperty("subClassData"));
    /** @noinspection PhpUndefinedMethodInspection */
    self::assertNull($entity->getSubClassData());
  }
//</editor-fold desc="Public Methods">
}