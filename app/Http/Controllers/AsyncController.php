<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/14/18
 * Time: 9:02 PM
 */

namespace App\Http\Controllers;


use App\Entity\AsyncRequest;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\JsonResponse;

/**
 * Class AsyncController
 * @package App\Http\Controllers
 */
class AsyncController
{
//<editor-fold desc="Public Methods">
  /**
   * @param string $id
   * @param EntityManagerInterface $entityManager
   * @return JsonResponse
   */
  public function getAsyncRequestState(string $id, EntityManagerInterface $entityManager)
  {
    /** @var AsyncRequest $request */
    $request = $entityManager->find(AsyncRequest::class, $id);
    if ($request === null) {
      return new JsonResponse(["type" => "0", "message" => "does not exist"]);
    }
    if ($request->getStartTime() === null) {
      return new JsonResponse(["type" => "1", "message" => "not yet started"]);
    }
    if ($request->getEndTime() === null) {
      return new JsonResponse(["type" => "2", "message" => "running", "progress" => $request->getProgress()]);
    }
    return new JsonResponse(["type" => "3", "message" => "finished", "result" => $request->getResult()]);
  }
//</editor-fold desc="Public Methods">
}