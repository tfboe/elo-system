<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/15/18
 * Time: 4:04 AM
 */

namespace App\Service\AsyncServices;


use App\Jobs\RecalculateRankingSystemsJob;
use App\Jobs\RunAsyncRequestJob;
use Doctrine\ORM\EntityManagerInterface;
use Tfboe\FmLib\Service\RankingSystemServiceInterface;

class RecalculateRankingSystems implements RecalculateRankingSystemsInterface
{
//<editor-fold desc="Fields">
  /**
   * @var RankingSystemServiceInterface
   */
  private $rss;

  private $em;
//</editor-fold desc="Fields">
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * CreateOrReplaceTournament constructor.
   * @param RankingSystemServiceInterface $rss
   */
  public function __construct(RankingSystemServiceInterface $rss, EntityManagerInterface $em)
  {
    $this->rss = $rss;
    $this->em = $em;
  }

//<editor-fold desc="Public Methods">

  /**
   * @param string $id
   * @return RunAsyncRequestJob
   */
  function getJob(string $id): RunAsyncRequestJob
  {
    return new RecalculateRankingSystemsJob($id);
  }

  /**
   * @param mixed $input
   * @param $reportProgress
   * @return array|mixed[]
   */
  function run($input, $reportProgress): array
  {
    $this->rss->recalculateRankingSystems();
    $this->em->flush();
    return ["data" => "success", "status" => 200];
  }
//</editor-fold desc="Public Methods">
}