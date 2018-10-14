<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/14/18
 * Time: 1:30 PM
 */

namespace App\Service;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\JsonResponse;

/**
 * Class AsyncRunner
 * @package App\Service
 */
class AsyncRunner implements AsyncRunnerInterface
{
//<editor-fold desc="Fields">
  /** @var Container */
  private $app;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * AsyncRunner constructor.
   */
  public function __construct(Container $app)
  {
    $this->app = $app;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @inheritdoc
   */
  public function runAsync($serviceName, $input, $reportProgress = null): array
  {
    try {
      if ($reportProgress === null) {
        $reportProgress = function ($progress) {
        };
      }
      /** @var AsyncServiceRunnerInterface $service */
      $service = $this->app->make($serviceName);
      return $service->run($input, $reportProgress);
    } catch (\Exception $e) {
      /** @var ExceptionHandler $handler */
      $handler = $this->app->make(ExceptionHandler::class);
      $resp = $handler->render(null, $e);
      if ($resp instanceof JsonResponse) {
        return [
          'data' => $resp->getData(),
          'status' => $resp->getStatusCode()
        ];
      } else {
        return [
          'data' => "Error was not handled successfully!",
          'status' => "500"
        ];
      }
    }
  }
//</editor-fold desc="Public Methods">
}