<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 12:33 AM
 */

class TestEntity extends \App\Entity\Helpers\BaseEntity
{
//<editor-fold desc="Fields">
  /** @var mixed $prop */
  public $prop = null;

  /** @var \App\Entity\User */
  public $user = null;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return mixed
   */
  public function getProp()
  {
    return $this->prop;
  }

  /**
   * @return \App\Entity\User
   */
  public function getUser(): \App\Entity\User
  {
    return $this->user;
  }

  /**
   * @param mixed $prop
   */
  public function setProp($prop)
  {
    $this->prop = $prop;
  }

  /**
   * @param \App\Entity\User $user
   */
  public function setUser(\App\Entity\User $user)
  {
    $this->user = $user;
  }
//</editor-fold desc="Public Methods">
}

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
    $this->assertEquals($value, $object->getProp());
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
    $this->assertEquals($object->getProp(), $value);
  }

  public function testReference()
  {
    $password = $this->newPassword();
    /** @var \App\Entity\User $user */
    $user = entity(\App\Entity\User::class)->create(['unhashedPassword' => $password]);
    $specification['user'] = ['reference' => \App\Entity\User::class];
    $object = new TestEntity();
    /** @var \App\Http\Controllers\UserController $controller */
    $controller = app(\App\Http\Controllers\UserController::class);
    $method = self::getMethod(\App\Http\Controllers\UserController::class, 'setFromSpecification');
    $method->invokeArgs($controller, [$object, $specification, ['user' => $user->getId()]]);
    $this->assertEquals($user, $object->getUser());
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
    $this->assertEquals($datetime, $object->getProp());
  }
//</editor-fold desc="Public Methods">
}