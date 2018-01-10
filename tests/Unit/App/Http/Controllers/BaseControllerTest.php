<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 12:33 AM
 */

namespace Tests\Unit\App\Http\Controllers;

use App\Entity\Helpers\BaseEntity;
use App\Entity\User;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\UserController;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Helpers\TestEnum;
use Tests\Helpers\UnitTestCase;

/**
 * Class BaseControllerTest
 * @package Tests\Unit\App\Http\Controllers
 */
class BaseControllerTest extends UnitTestCase
{
  //tests also private method disable this tests as soon as all are used in public interfaces
//<editor-fold desc="Public Methods">

  /**
   * @covers \App\Http\Controllers\BaseController::__construct
   */
  public function testConstruct()
  {
    $entityManager = $this->createMock(EntityManagerInterface::class);
    $controller = $this->getMockForAbstractClass(BaseController::class, [
      $entityManager
    ]);
    self::assertInstanceOf(BaseController::class, $controller);
    self::assertEquals($entityManager, self::getProperty(get_class($controller), 'entityManager')
      ->getValue($controller));
  }

  /**
   * @covers \App\Http\Controllers\BaseController::datetimetzTransformer()
   * @uses   \App\Http\Controllers\BaseController::__construct
   */
  public function testDatetimetzTransformer()
  {
    $controller = $this->controller();
    $closure = self::getMethod(BaseController::class, 'datetimetzTransformer')
      ->invokeArgs($controller, [TestEnum::class]);
    $string = "2017-01-01 00:00:00 Europe/Vienna";
    $datetime = new \DateTime($string);
    /** @var \DateTime $result */
    $result = $closure($string);
    self::assertEquals($datetime, $result);
    self::assertEquals($datetime->getTimezone(), $result->getTimezone());
  }

  /**
   * @covers \App\Http\Controllers\BaseController::enumTransformer
   * @uses   \App\Helpers\BasicEnum
   * @uses   \App\Http\Controllers\BaseController::__construct
   */
  public function testEnumTransformer()
  {
    $controller = $this->controller();
    $closure = self::getMethod(BaseController::class, 'enumTransformer')->invokeArgs($controller, [TestEnum::class]);
    self::assertEquals(1, $closure('INT_KEY'));
    self::assertEquals('value', $closure('KEY'));
  }

  /**
   * @covers \App\Http\Controllers\BaseController::setFromSpecification
   * @uses   \App\Entity\Helpers\BaseEntity::methodExists
   * @uses   \App\Http\Controllers\BaseController::__construct
   */
  public function testSetFromSpecificationWithDefault()
  {
    $value = "test-value";
    $specification['prop'] = ['default' => $value];
    $object = self::getMockForAbstractClass(BaseEntity::class, [], '', true, true, true, ['setProp']);
    $object->expects(static::once())->method('setProp')->with($value)->willReturnSelf();
    $controller = $this->controller();
    $method = self::getMethod(UserController::class, 'setFromSpecification');
    $method->invokeArgs($controller, [$object, $specification, []]);
  }

  /**
   * @covers \App\Http\Controllers\BaseController::setFromSpecification
   * @uses   \App\Http\Controllers\BaseController::__construct
   * @uses   \App\Http\Controllers\BaseController::transformValue
   */
  public function testSetFromSpecificationWithProperty()
  {
    $value = 'test-value';
    $specification['attr'] = ['property' => 'prop'];
    $object = self::getMockForAbstractClass(BaseEntity::class, [], '', true, true, true, ['setProp']);
    $object->expects(static::once())->method('setProp')->with($value)->willReturnSelf();
    $controller = $this->controller();
    $method = self::getMethod(UserController::class, 'setFromSpecification');
    $method->invokeArgs($controller, [$object, $specification, ['attr' => $value]]);
  }

  /**
   * @covers \App\Http\Controllers\BaseController::transformValue
   * @uses   \App\Http\Controllers\BaseController::__construct
   */
  public function testTransformValueByReference()
  {
    $user = "resultUser";
    $specification = ['reference' => User::class];
    $value = 'user-id';

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $entityManager->expects(static::once())->method('find')->with(User::class, 'user-id')->willReturn($user);
    $controller = $this->getMockForAbstractClass(BaseController::class, [$entityManager]);
    $method = self::getMethod(UserController::class, 'transformValue');
    /** @noinspection PhpUnhandledExceptionInspection */
    $method->invokeArgs($controller, [&$value, $specification]);

    self::assertTrue($value === $user);
  }

  /**
   * @covers \App\Http\Controllers\BaseController::transformValue
   * @uses   \App\Http\Controllers\BaseController::__construct
   */
  public function testTransformValueByTransformer()
  {
    $value = "5";
    $transformer = function ($input) {
      self::assertEquals("5", $input);
      return 6;
    };
    $specification = ['transformer' => $transformer];

    $controller = $this->controller();
    $method = self::getMethod(UserController::class, 'transformValue');
    $method->invokeArgs($controller, [&$value, $specification]);

    self::assertEquals(6, $value);
  }

  /**
   * @covers \App\Http\Controllers\BaseController::transformValue
   * @covers \App\Http\Controllers\BaseController::transformByType
   * @uses   \App\Http\Controllers\BaseController::__construct
   */
  public function testTransformValueByTypeDateTime()
  {
    $value = "2005-02-28 16:35:01";
    $datetime = new \DateTime($value);
    $specification = ['type' => 'datetime'];

    $controller = $this->controller();
    $method = self::getMethod(UserController::class, 'transformValue');
    $method->invokeArgs($controller, [&$value, $specification]);

    self::assertEquals($datetime, $value);
  }

  /**
   * @covers \App\Http\Controllers\BaseController::transformValue
   * @covers \App\Http\Controllers\BaseController::transformByType
   * @uses   \App\Http\Controllers\BaseController::__construct
   */
  public function testTransformValueByTypeDefault()
  {
    $value = "2005-02-28 16:35:01";
    $specification = ['type' => 'default'];

    $controller = $this->controller();
    $method = self::getMethod(UserController::class, 'transformValue');
    $method->invokeArgs($controller, [&$value, $specification]);

    self::assertEquals("2005-02-28 16:35:01", $value);
  }

  /**
   * @covers \App\Http\Controllers\BaseController::validateBySpecification
   * @uses   \App\Http\Controllers\BaseController::__construct
   */
  public function testValidateBySpecification()
  {
    $controller = $this->getMockForAbstractClass(BaseController::class,
      [$this->createMock(EntityManagerInterface::class)], '', true, true, true, ['validate']);
    $request = $this->createMock(Request::class);
    $controller->expects(static::once())->method('validate')
      ->with($request, ['withValidation' => 'required|string|min:2']);
    /** @var BaseController $controller */
    $specification = [
      'noValidation' => ['default' => 5],
      'withValidation' => ['validation' => 'required|string|min:2']
    ];
    $method = self::getMethod(UserController::class, 'validateBySpecification');
    $method->invokeArgs($controller, [$request, $specification]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|BaseController
   */
  private function controller(): MockObject
  {
    return $this->getMockForAbstractClass(BaseController::class, [
      $this->createMock(EntityManagerInterface::class)
    ]);
  }
//</editor-fold desc="Private Methods">
}