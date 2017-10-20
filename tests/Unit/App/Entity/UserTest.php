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
use App\Exceptions\ValueNotSet;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\TestCase;

/**
 * Class UserTest
 * @package Tests\Unit\App\Entity
 */
class UserTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testConstructor()
  {
    $user = $this->user();
    self::assertInstanceOf(User::class, $user);
    self::assertEquals(['ver' => 1], $user->getJWTCustomClaims());
    self::assertEquals(1, $user->getJwtVersion());
    self::assertEquals(0, $user->getLastConfirmedAGBVersion());
  }

  public function testEmail()
  {
    $user = $this->user();
    $user->setEmail("test@a1.net");
    self::assertEquals("test@a1.net", $user->getEmail());
  }

  public function testEmailException()
  {
    $user = $this->user();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property email of the class App\Entity\User must be set before it can " .
      "be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    $user->getEmail();
  }

  public function testId()
  {
    $user = $this->user();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($user);
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $user->getId());
  }

  public function testIdException()
  {
    $user = $this->user();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property id of the class App\Entity\User must be set before it can " .
      "be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    $user->getId();
  }

  public function testJWTCustomClaims()
  {
    $user = $this->user();
    $user->setJwtVersion(5);
    self::assertEquals(['ver' => 5], $user->getJWTCustomClaims());
  }

  public function testJWTIdentifier()
  {
    $user = $this->user();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($user);
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $user->getJWTIdentifier());
    self::assertEquals($user->getId(), $user->getJWTIdentifier());
  }

  public function testJWTIdentifierException()
  {
    $user = $this->user();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property id of the class App\Entity\User must be set before it can " .
      "be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    $user->getJWTIdentifier();
  }

  public function testJwtVersion()
  {
    $user = $this->user();
    $user->setJwtVersion(5);
    self::assertEquals(5, $user->getJwtVersion());
  }

  public function testLastConfirmedAGBVersion()
  {
    $user = $this->user();
    $user->setLastConfirmedAGBVersion(5);
    self::assertEquals(5, $user->getLastConfirmedAGBVersion());
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