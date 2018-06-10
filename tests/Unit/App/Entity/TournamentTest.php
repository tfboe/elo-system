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
 * Class Tournament
 * @package Tests\Unit\App\Entity
 */
class TournamentTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Tournament::__construct
   */
  public function testConstructor()
  {
    $entity = new \App\Entity\Tournament();
    self::assertInstanceOf(\App\Entity\Tournament::class, $entity);
  }
//</editor-fold desc="Public Methods">
}