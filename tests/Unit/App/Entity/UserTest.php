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
 * Class User
 * @package Tests\Unit\App\Entity
 */
class UserTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\User::__construct
   */
  public function testConstructor()
  {
    $entity = new \App\Entity\User();
    self::assertInstanceOf(\App\Entity\User::class, $entity);
  }
//</editor-fold desc="Public Methods">
}