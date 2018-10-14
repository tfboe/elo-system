<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/14/18
 * Time: 11:22 AM
 */

namespace App\Http\Controllers;


use App\Entity\AsyncRequest;
use App\Service\AsyncRunnerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Container\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tfboe\FmLib\Http\Controllers\BaseController;
use Tfboe\FmLib\Service\AsyncExecuterService;

abstract class AsyncableController extends BaseController
{
//<editor-fold desc="Fields">
  /** @var AsyncRunnerInterface */
  private $ars;

  /** @var AsyncExecuterService */
  private $aes;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * AsyncableController constructor.
   * @param EntityManagerInterface $entityManager
   * @param Container $app
   * @param AsyncExecuterService $aes
   */
  public function __construct(EntityManagerInterface $entityManager, AsyncRunnerInterface $ars,
                              AsyncExecuterService $aes)
  {
    parent::__construct($entityManager);
    $this->ars = $ars;
    $this->aes = $aes;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Protected Methods">
  /**
   * @param Request $request
   * @param string $serviceName
   * @return JsonResponse
   */
  protected function checkAsync(Request $request, string $serviceName): JsonResponse
  {
    $input = $request->input();
    if (\Auth::user() !== null) {
      $input['user'] = \Auth::user()->getId();
    }
    if ($request->has('async') && $request->get('async') === true) {
      return $this->runAsync($input, $serviceName);
    } else {
      $result = $this->ars->runAsync($serviceName, $input);
      return new JsonResponse($result['data'], $result['status']);
    }
  }

  /**
   * @param $input
   * @param string $serviceName
   * @return JsonResponse
   */
  protected function runAsync($input, string $serviceName): JsonResponse
  {
    $asyncRequest = new AsyncRequest(["input" => $input], $serviceName);
    $this->getEntityManager()->persist($asyncRequest);
    $this->getEntityManager()->flush();
    $this->aes->runBashCommand(env('PHP_COMMAND', 'php') . ' ../artisan run-async-request ' .
      $asyncRequest->getId());
    return new JsonResponse(["type" => "async", "result" => "Started successfully",
      "async-id" => $asyncRequest->getId()]);
  }
//</editor-fold desc="Protected Methods">
}