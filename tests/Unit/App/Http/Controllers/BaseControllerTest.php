<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 12:33 AM
 */

namespace Tests\Unit\App\Http\Controllers;

use App\Entity\User;
use App\Http\Controllers\UserController;
use Tests\Helpers\TestCase;
use Tests\Helpers\TestEntity;

/**
 * Class BaseControllerTest
 * @package Tests\Unit\App\Http\Controllers
 */
class BaseControllerTest extends TestCase
{
  //tests also private method disable this tests as soon as all are used in public interfaces
//<editor-fold desc="Public Methods">
  public function testDefault()
  {
    $value = "test-value";
    $specification['prop'] = ['default' => $value];
    $object = new TestEntity();
    /** @var UserController $controller */
    $controller = app(UserController::class);
    $method = self::getMethod(UserController::class, 'setFromSpecification');
    $method->invokeArgs($controller, [$object, $specification, []]);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($value, $object->getProp());
  }

  public function testDefaultTransformByType()
  {
    $value = "2005-02-28 16:35:01";
    $specification['prop'] = ['type' => 'default'];
    $object = new TestEntity();
    /** @var UserController $controller */
    $controller = app(UserController::class);
    $method = self::getMethod(UserController::class, 'setFromSpecification');
    $method->invokeArgs($controller, [$object, $specification, ['prop' => $value]]);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($value, $object->getProp());
  }

  public function testProperty()
  {
    $specification['attr'] = ['property' => 'prop'];
    $object = new TestEntity();
    /** @var UserController $controller */
    $controller = app(UserController::class);
    $method = self::getMethod(UserController::class, 'setFromSpecification');
    $value = 'test-value';
    $method->invokeArgs($controller, [$object, $specification, ['attr' => $value]]);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($object->getProp(), $value);
  }

  public function testReference()
  {
    $password = 'testPassword';
    /** @var User $user */
    $user = entity(User::class)->create(['originalPassword' => $password]);
    $specification['user'] = ['reference' => User::class];
    $object = new TestEntity();
    /** @var UserController $controller */
    $controller = app(UserController::class);
    $method = self::getMethod(UserController::class, 'setFromSpecification');
    /** @noinspection PhpUnhandledExceptionInspection */
    $method->invokeArgs($controller, [$object, $specification, ['user' => $user->getId()]]);
    self::assertEquals($user, $object->getUser());
  }

  public function testTransformByType()
  {
    $value = "2005-02-28 16:35:01";
    $datetime = new \DateTime($value);
    $specification['prop'] = ['type' => 'datetime'];
    $object = new TestEntity();
    /** @var UserController $controller */
    $controller = app(UserController::class);
    $method = self::getMethod(UserController::class, 'setFromSpecification');
    $method->invokeArgs($controller, [$object, $specification, ['prop' => $value]]);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($datetime, $object->getProp());
  }
//</editor-fold desc="Public Methods">
}