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
 * Class Game
 * @package Tests\Unit\App\Entity
 */
class GameTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Game::__construct
   */
  public function testConstructor()
  {
    $entity = new \App\Entity\Game();
    self::assertInstanceOf(\App\Entity\Game::class, $entity);
  }
//</editor-fold desc="Public Methods">
}