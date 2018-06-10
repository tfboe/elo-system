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
 * Class Ranking
 * @package Tests\Unit\App\Entity
 */
class RankingTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Ranking::__construct
   */
  public function testConstructor()
  {
    $entity = new \App\Entity\Ranking();
    self::assertInstanceOf(\App\Entity\Ranking::class, $entity);
  }
//</editor-fold desc="Public Methods">
}