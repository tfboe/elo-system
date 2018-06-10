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
 * Class Match
 * @package Tests\Unit\App\Entity
 */
class MatchTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Match::__construct
   */
  public function testConstructor()
  {
    $entity = new \App\Entity\Match();
    self::assertInstanceOf(\App\Entity\Match::class, $entity);
  }
//</editor-fold desc="Public Methods">
}