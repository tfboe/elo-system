<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/14/18
 * Time: 1:24 PM
 */

namespace App\Console\Commands;


use App\Entity\AsyncRequest;
use App\Service\AsyncRunnerInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Console\Command;

class RunAsyncRequestCommand extends Command
{
//<editor-fold desc="Fields">
  /**
   * The console command signature.
   *
   * @var string
   */
  protected $signature = 'run-async-request {id}';
  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = "Runs the given async request";
//</editor-fold desc="Fields">
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * Execute the console command.
   *
   * @return void
   */
  public function handle(EntityManagerInterface $entityManager, AsyncRunnerInterface $asyncRunner)
  {
    $id = $this->argument('id');
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
}