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
 * Class Competition
 * @package Tests\Unit\App\Entity
 */
class CompetitionTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Competition::__construct
   */
  public function testConstructor()
  {
    $entity = new \App\Entity\Competition();
    self::assertInstanceOf(\App\Entity\Competition::class, $entity);
  }
//</editor-fold desc="Public Methods">
}