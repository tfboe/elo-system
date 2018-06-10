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
 * Class Team
 * @package Tests\Unit\App\Entity
 */
class TeamTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Team::__construct
   */
  public function testConstructor()
  {
    $entity = new \App\Entity\Team();
    self::assertInstanceOf(\App\Entity\Team::class, $entity);
  }
//</editor-fold desc="Public Methods">
}