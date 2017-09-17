<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 12:33 AM
 */

class BaseControllerTest extends DatabaseTestCase
{
//<editor-fold desc="Public Methods">
  public function testDefaultTransformByType()
  {
    $value = "2005-02-28 16:35:01";
    $specification['prop'] = ['type' => 'default'];
    $object = new TestEntity();
    /** @var \App\Http\Controllers\UserController $controller */
    $controller = app(\App\Http\Controllers\UserController::class);
    $method = self::getMethod(\App\Http\Controllers\UserController::class, 'setFromSpecification');
    $method->invokeArgs($controller, [$object, $specification, ['prop' => $value]]);
    self::assertEquals($value, $object->getProp());
  }

  public function testProperty()
  {
    $specification['attr'] = ['property' => 'prop'];
    $object = new TestEntity();
    /** @var \App\Http\Controllers\UserController $controller */
    $controller = app(\App\Http\Controllers\UserController::class);
    $method = self::getMethod(\App\Http\Controllers\UserController::class, 'setFromSpecification');
    $value = 'test-value';
    $method->invokeArgs($controller, [$object, $specification, ['attr' => $value]]);
    self::assertEquals($object->getProp(), $value);
  }

  public function testReference()
  {
    $password = $this->newPassword();
    /** @var \App\Entity\User $user */
    $user = entity(\App\Entity\User::class)->create(['originalPassword' => $password]);
    $specification['user'] = ['reference' => \App\Entity\User::class];
    $object = new TestEntity();
    /** @var \App\Http\Controllers\UserController $controller */
    $controller = app(\App\Http\Controllers\UserController::class);
    $method = self::getMethod(\App\Http\Controllers\UserController::class, 'setFromSpecification');
    $method->invokeArgs($controller, [$object, $specification, ['user' => $user->getId()]]);
    self::assertEquals($user, $object->getUser());
  }

  public function testTransformByType()
  {
    $value = "2005-02-28 16:35:01";
    $datetime = new DateTime($value);
    $specification['prop'] = ['type' => 'datetime'];
    $object = new TestEntity();
    /** @var \App\Http\Controllers\UserController $controller */
    $controller = app(\App\Http\Controllers\UserController::class);
    $method = self::getMethod(\App\Http\Controllers\UserController::class, 'setFromSpecification');
    $method->invokeArgs($controller, [$object, $specification, ['prop' => $value]]);
    self::assertEquals($datetime, $object->getProp());
  }

  public function testDefault()
  {
    $value = "test-value";
    $specification['prop'] = ['default' => $value];
    $object = new TestEntity();
    /** @var \App\Http\Controllers\UserController $controller */
    $controller = app(\App\Http\Controllers\UserController::class);
    $method = self::getMethod(\App\Http\Controllers\UserController::class, 'setFromSpecification');
    $method->invokeArgs($controller, [$object, $specification, []]);
    self::assertEquals($value, $object->getProp());
  }
//</editor-fold desc="Public Methods">
}