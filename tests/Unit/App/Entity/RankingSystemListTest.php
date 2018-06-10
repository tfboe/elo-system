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
 * Class RankingSystemList
 * @package Tests\Unit\App\Entity
 */
class RankingSystemListTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\RankingSystemList::__construct
   */
  public function testConstructor()
  {
    $entity = new \App\Entity\RankingSystemList();
    self::assertInstanceOf(\App\Entity\RankingSystemList::class, $entity);
  }
//</editor-fold desc="Public Methods">
}