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
use Doctrine\ORM\EntityManagerInterface;

abstract class RunAsyncRequestJob extends Job
{
//<editor-fold desc="Fields">
  /** @var string */
  protected $id;
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
    $request = $entityManager->find(AsyncRequest::class, $id);
    $request->setStartTime(new \DateTime());
    $entityManager->flush();
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