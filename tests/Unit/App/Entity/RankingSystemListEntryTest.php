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
 * Class RankingSystemListEntry
 * @package Tests\Unit\App\Entity
 */
class RankingSystemListEntryTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\RankingSystemListEntry::__construct
   */
  public function testConstructor()
  {
    $entity = new \App\Entity\RankingSystemListEntry(["subclassData"]);
    self::assertInstanceOf(\App\Entity\RankingSystemListEntry::class, $entity);
    self::assertTrue($entity->hasProperty("subClassData"));
    /** @noinspection PhpUndefinedMethodInspection */
    self::assertNull($entity->getSubClassData());
  }
//</editor-fold desc="Public Methods">
}