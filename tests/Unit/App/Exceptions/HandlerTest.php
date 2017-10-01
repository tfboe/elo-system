<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tests\Unit\App\Exceptions;


use App\Exceptions\Handler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
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
    self::assertEquals(['status' => 500, 'message' => 'Exception message'], $res->getData(true));

    $exception = new \Exception("Exception message", 402);
    $res = $handler->render($this->request(), $exception);
    self::assertInstanceOf(JsonResponse::class, $res);
    /** @var JsonResponse $res */
    self::assertEquals(['status' => 402, 'message' => 'Exception message'], $res->getData(true));
  }

  public function testRenderValidationErrors()
  {
    $handler = $this->handler();
    $base_handler = new \Laravel\Lumen\Exceptions\Handler();
    /** @var \Illuminate\Validation\Validator $validator */
    /** @noinspection PhpUndefinedMethodInspection */
    $validator = Validator::make([], ['username' => 'required|min:6']);
    self::assertTrue($validator->fails());
    $exception = new ValidationException($validator);
    $request = $this->request();
    self::assertEquals($base_handler->render($request, $exception), $handler->render($request, $exception));
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