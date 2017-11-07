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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Tests\Helpers\TestCase;

/**
 * Class HandlerTest
 * @package Tests\Unit\App\Exceptions
 */
class HandlerTest extends TestCase
{
//<editor-fold desc="Public Methods">
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

    $exception = new PlayerAlreadyExists([]);
    $res = $handler->render($this->request(), $exception);
    self::assertInstanceOf(JsonResponse::class, $res);
    /** @var JsonResponse $res */
    self::assertEquals(['status' => 409, 'message' => 'Some players do already exist',
      'name' => 'PlayerAlreadyExistsException', 'players' => []], $res->getData(true));
  }

  public function testRenderValidationErrors()
  {
    $handler = $this->handler();
    /** @var \Illuminate\Validation\Validator $validator */
    /** @noinspection PhpUndefinedMethodInspection */
    $validator = Validator::make([], ['username' => 'required|min:6']);
    self::assertTrue($validator->fails());
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