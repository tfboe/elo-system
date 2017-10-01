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
use Tests\Helpers\TestCase;

class UserTest extends TestCase
{
//<editor-fold desc="Public Methods">
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

  public function testGetJWTCustomClaims()
  {
    $user = $this->user();
    self::assertEquals(['ver' => 1], $user->getJWTCustomClaims());
    $user->setJwtVersion(5);
    self::assertEquals(['ver' => 5], $user->getJWTCustomClaims());
  }

  public function testGetJWTIdentifierException()
  {
    $user = $this->user();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property id of the class App\Entity\User must be set before it can " .
      "be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    $user->getJWTIdentifier();
  }

  public function testIdException()
  {
    $user = $this->user();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property id of the class App\Entity\User must be set before it can " .
      "be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    $user->getId();
  }

  public function testJwtVersion()
  {
    $user = $this->user();
    self::assertEquals(1, $user->getJwtVersion());
    $user->setJwtVersion(5);
    self::assertEquals(5, $user->getJwtVersion());
  }

  public function testLastConfirmedAGBVersion()
  {
    $user = $this->user();
    self::assertEquals(0, $user->getLastConfirmedAGBVersion());
    $user->setLastConfirmedAGBVersion(5);
    self::assertEquals(5, $user->getLastConfirmedAGBVersion());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  private function user(): User
  {
    return new User();
  }
//</editor-fold desc="Private Methods">
}