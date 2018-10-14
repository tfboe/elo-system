<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/14/18
 * Time: 6:19 PM
 */

namespace App\Jobs;

use App\Entity\AsyncRequest;
use App\Service\AsyncRunnerInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class RunAsyncRequest extends Job
{
//<editor-fold desc="Fields">
  /** @var string */
  private $id;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * RunAsyncRequest constructor.
   * @param string $id the async request id
   */
  public function __construct(string $id)
  {
    $this->id = $id;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle(EntityManagerInterface $entityManager, AsyncRunnerInterface $asyncRunner)
  {
    $id = $this->id;
    /** @var AsyncRequest $request */
    $entityManager->transactional(function (EntityManager $em) use ($id, $asyncRunner) {
      /** @var AsyncRequest $request */
      $request = $em->find(AsyncRequest::class, $id, LockMode::PESSIMISTIC_WRITE);
      if ($request->getStartTime() !== null) {
        throw new \Exception("Async Request is already running!");
      }
      $request->setStartTime(new \DateTime());
      $em->flush();
    });
    $request = $entityManager->find(AsyncRequest::class, $id);
    $reportProgress = function ($progress) use ($request) {
      $request->setProgress($progress);
    };
    $result = $asyncRunner->runAsync($request->getServiceName(), $request->getInput()['input'], $reportProgress);
    $request->setProgress(1.0);
    $request->setEndTime(new \DateTime());
    $request->setResult($result);
    $entityManager->flush();
  }
//</editor-fold desc="Public Methods">
}