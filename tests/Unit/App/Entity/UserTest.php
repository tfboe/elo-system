<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tests\Unit\App\Entity;

use App\Entity\User;
use Tests\Helpers\UnitTestCase;

/**
 * Class UserTest
 * @package Tests\Unit\App\Entity
 */
class UserTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\User::__construct
   * @uses   \App\Entity\User::getJWTCustomClaims
   * @uses   \App\Entity\User::getJwtVersion
   * @uses   \App\Entity\User::getConfirmedAGBVersion
   */
  public function testConstructor()
  {
    $user = $this->user();
    self::assertInstanceOf(User::class, $user);
    self::assertEquals(['ver' => 1], $user->getJWTCustomClaims());
    self::assertEquals(1, $user->getJwtVersion());
    self::assertEquals(0, $user->getConfirmedAGBVersion());
  }

  /**
   * @covers \App\Entity\User::setEmail
   * @covers \App\Entity\User::getEmail
   * @uses   \App\Entity\User::__construct
   */
  public function testEmail()
  {
    $user = $this->user();
    $user->setEmail("test@a1.net");
    self::assertEquals("test@a1.net", $user->getEmail());
  }

  /**
   * @covers \App\Entity\User::getJWTCustomClaims
   * @uses   \App\Entity\User::setJwtVersion
   * @uses   \App\Entity\User::__construct
   */
  public function testJWTCustomClaims()
  {
    $user = $this->user();
    $user->setJwtVersion(5);
    self::assertEquals(['ver' => 5], $user->getJWTCustomClaims());
  }

  /**
   * @covers \App\Entity\User::getJWTIdentifier
   * @uses   \App\Entity\User::__construct
   * @uses   \App\Entity\Helpers\UUIDEntity::getId
   */
  public function testJWTIdentifier()
  {
    $user = $this->user();
    /** @noinspection PhpUnhandledExceptionInspection */
    self::getProperty(get_class($user), 'id')->setValue($user, 'user-id');
    self::assertEquals('user-id', $user->getJWTIdentifier());
    self::assertEquals($user->getId(), $user->getJWTIdentifier());
  }

  /**
   * @covers \App\Entity\User::getJwtVersion()
   * @covers \App\Entity\User::setJwtVersion
   * @uses   \App\Entity\User::__construct
   */
  public function testJwtVersion()
  {
    $user = $this->user();
    $user->setJwtVersion(5);
    self::assertEquals(5, $user->getJwtVersion());
  }

  /**
   * @covers \App\Entity\User::getConfirmedAGBVersion
   * @covers \App\Entity\User::setConfirmedAGBVersion
   * @uses   \App\Entity\User::__construct
   */
  public function testLastConfirmedAGBVersion()
  {
    $user = $this->user();
    $user->setConfirmedAGBVersion(5);
    self::assertEquals(5, $user->getConfirmedAGBVersion());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return User a new user
   */
  private function user(): User
  {
    return new User();
  }
//</editor-fold desc="Private Methods">
}