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
 * Class Phase
 * @package Tests\Unit\App\Entity
 */
class PhaseTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Phase::__construct
   */
  public function testConstructor()
  {
    $entity = new \App\Entity\Phase();
    self::assertInstanceOf(\App\Entity\Phase::class, $entity);
  }
//</editor-fold desc="Public Methods">
}