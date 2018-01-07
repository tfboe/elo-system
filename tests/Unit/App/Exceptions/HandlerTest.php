<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tests\Unit\App\Exceptions;


use App\Exceptions\AuthenticationException;
use App\Exceptions\DuplicateException;
use App\Exceptions\Handler;
use App\Exceptions\PlayerAlreadyExists;
use App\Exceptions\ReferenceException;
use App\Exceptions\UnorderedPhaseNumberException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tests\Helpers\UnitTestCase;

/**
 * Class HandlerTest
 * @package Tests\Unit\App\Exceptions
 */
class HandlerTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Exceptions\Handler::render
   * @covers \App\Exceptions\Handler::getExceptionHTTPStatusCode
   * @covers \App\Exceptions\Handler::getExceptionName
   * @covers \App\Exceptions\Handler::getJsonMessage
   * @uses   \App\Exceptions\AuthenticationException
   * @uses   \App\Exceptions\DuplicateException
   * @uses   \App\Exceptions\PlayerAlreadyExists
   * @uses   \App\Exceptions\ReferenceException
   * @uses   \App\Exceptions\UnorderedPhaseNumberException
   */
  public function testRender()
  {
    $handler = $this->handler();
    $exception = new \Exception("Exception message");
    $res = $handler->render($this->request(), $exception);
    self::assertInstanceOf(JsonResponse::class, $res);
    /** @var JsonResponse $res */
    self::assertEquals(['status' => 500, 'message' => 'Exception message', 'name' => 'InternalException'],
      $res->getData(true));

    $exception = new \Exception("Exception message", 402);
    $res = $handler->render($this->request(), $exception);
    self::assertInstanceOf(JsonResponse::class, $res);
    /** @var JsonResponse $res */
    self::assertEquals(['status' => 402, 'message' => 'Exception message', 'name' => 'InternalException'],
      $res->getData(true));

    $exception = new AuthenticationException("Authentication Exception message");
    $res = $handler->render($this->request(), $exception);
    self::assertInstanceOf(JsonResponse::class, $res);
    /** @var JsonResponse $res */
    self::assertEquals(['status' => 401, 'message' => 'Authentication Exception message',
      'name' => 'AuthenticationException'], $res->getData(true));

    $exception = new DuplicateException('value', 'name', 'array');
    $res = $handler->render($this->request(), $exception);
    self::assertInstanceOf(JsonResponse::class, $res);
    /** @var JsonResponse $res */
    self::assertEquals(['status' => 409, 'message' => 'Duplicate Exception',
      'name' => 'DuplicateException', 'duplicateValue' => 'value', 'arrayName' => 'array'], $res->getData(true));

    $exception = new ReferenceException('value', 'name');
    $res = $handler->render($this->request(), $exception);
    self::assertInstanceOf(JsonResponse::class, $res);
    /** @var JsonResponse $res */
    self::assertEquals(['status' => 409, 'message' => 'Reference Exception',
      'name' => 'ReferenceException', 'referenceValue' => 'value', 'referenceName' => 'name'], $res->getData(true));

    $exception = new UnorderedPhaseNumberException(2, 1);
    $res = $handler->render($this->request(), $exception);
    self::assertInstanceOf(JsonResponse::class, $res);
    /** @var JsonResponse $res */
    self::assertEquals(['status' => 409, 'message' => 'Unordered Phase Number Exception',
      'name' => 'UnorderedPhaseNumberException', 'previousPhaseNumber' => 2, 'nextPhaseNumber' => 1],
      $res->getData(true));

    $exception = new PlayerAlreadyExists([]);
    $res = $handler->render($this->request(), $exception);
    self::assertInstanceOf(JsonResponse::class, $res);
    /** @var JsonResponse $res */
    self::assertEquals(['status' => 409, 'message' => 'Some players do already exist',
      'name' => 'PlayerAlreadyExistsException', 'players' => []], $res->getData(true));
  }

  /**
   * @covers \App\Exceptions\Handler::render
   * @covers \App\Exceptions\Handler::getExceptionHTTPStatusCode
   * @covers \App\Exceptions\Handler::getExceptionName
   * @covers \App\Exceptions\Handler::getJsonMessage
   */
  public function testRenderValidationErrors()
  {
    $handler = $this->handler();
    $validator = $this->createMock(Validator::class);
    $errors = $this->createMock(MessageBag::class);
    $errors->method('messages')->willReturn(['username' => ['The username field is required.']]);
    $validator->method('errors')->willReturn($errors);
    /** @var \Illuminate\Validation\Validator $validator */
    $exception = new ValidationException($validator, new Response('', 422));
    $request = $this->request();
    $res = $handler->render($request, $exception);
    self::assertInstanceOf(JsonResponse::class, $res);
    /** @var JsonResponse $res */
    self::assertEquals(['message' => 'The given data was invalid.', 'errors' =>
      ['username' => ['The username field is required.']],
      'status' => 422, 'name' => 'ValidationException'], $res->getData(true));
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return Handler a new handler
   */
  private function handler()
  {
    return new Handler();
  }

  /**
   * @return Request a new request
   */
  private function request()
  {
    return new Request();
  }
//</editor-fold desc="Private Methods">
}