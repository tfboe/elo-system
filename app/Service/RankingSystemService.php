<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/4/18
 * Time: 4:09 PM
 */

namespace App\Service;


use App\Entity\Helpers\TreeStructureEntityInterface;
use App\Entity\RankingSystem;
use App\Entity\Tournament;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class RankingSystemService
 * @package App\Service
 */
class RankingSystemService implements RankingSystemServiceInterface
{
//<editor-fold desc="Fields">
  /**
   * @var DynamicServiceLoadingServiceInterface
   */
  private $dsls;

  /** @var EntityManagerInterface */
  private $em;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">

  /**
   * RankingSystemService constructor.
   * @param DynamicServiceLoadingServiceInterface $dsls
   * @param EntityManagerInterface $em
   */
  public function __construct(DynamicServiceLoadingServiceInterface $dsls, EntityManagerInterface $em)
  {
    $this->dsls = $dsls;
    $this->em = $em;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function applyRankingSystems(Tournament $tournament, array $earliest_influences): void
  {
    $ranking_systems = $this->getRankingSystems($tournament);
    foreach ($ranking_systems as $sys) {
      if (!array_key_exists($sys->getId(), $earliest_influences)) {
        $earliest_influences[$sys->getId()] = [
          "rankingSystem" => $sys,
          "earliestInfluence" => null
        ];
      }
    }
    foreach ($earliest_influences as $arr) {
      /** @var RankingSystem $ranking */
      $ranking = $arr["rankingSystem"];
      $earliest_influence = $arr["earliestInfluence"];
      $service = $this->dsls->loadRankingSystemService($ranking->getServiceName());
      $service->updateRankingForTournament($ranking, $tournament, $earliest_influence);
    }
  }

  /**
   * @inheritDoc
   */
  public function recalculateRankingSystems(): void
  {
    $query = $this->em->createQueryBuilder();
    $query
      ->from(RankingSystem::class, 's')
      ->select('s')
      ->where($query->expr()->isNotNull('s.openSyncFrom'));
    /** @var RankingSystem[] $ranking_systems */
    $ranking_systems = $query->getQuery()->getResult();
    foreach ($ranking_systems as $ranking_system) {
      $service = $this->dsls->loadRankingSystemService($ranking_system->getServiceName());
      $service->updateRankingFrom($ranking_system, $ranking_system->getOpenSyncFrom());
      $ranking_system->setOpenSyncFrom(null);
    }
  }

  /**
   * @inheritDoc
   */
  public function adaptOpenSyncFromValues(Tournament $tournament, array $old_earliest_influences): void
  {
    $earliest_influences = $this->getRankingSystemsEarliestInfluences($tournament);
    foreach ($old_earliest_influences as $id => $arr) {
      if (array_key_exists($id, $earliest_influences)) {
        if ($old_earliest_influences[$id]["earliestInfluence"] < $earliest_influences[$id]["earliestInfluence"]) {
          $earliest_influences[$id]["earliestInfluence"] = $old_earliest_influences[$id]["earliestInfluence"];
        }
      } else {
        $earliest_influences[$id] = $old_earliest_influences[$id];
      }
    }
    foreach ($earliest_influences as $arr) {
      /** @var RankingSystem $ranking */
      $ranking = $arr["rankingSystem"];
      $earliest_influence = $arr["earliestInfluence"];
      if ($ranking->getOpenSyncFrom() === null || $ranking->getOpenSyncFrom() > $earliest_influence) {
        $ranking->setOpenSyncFrom($earliest_influence);
      }
    }
  }

  /**
   * @inheritDoc
   */
  public function getRankingSystemsEarliestInfluences(Tournament $tournament): array
  {
    $ranking_systems = $this->getRankingSystems($tournament);

    $result = [];
    //compute earliest influences
    foreach ($ranking_systems as $sys) {
      $service = $this->dsls->loadRankingSystemService($sys->getServiceName());
      $result[$sys->getId()] = [
        "rankingSystem" => $sys,
        "earliestInfluence" => $service->getEarliestInfluence($sys, $tournament)
      ];
    }

    return $result;
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @param TreeStructureEntityInterface $entity
   * @return RankingSystem[]
   */
  private function getRankingSystems(TreeStructureEntityInterface $entity): array
  {
    $result = $entity->getRankingSystems()->toArray();
    foreach ($entity->getChildren() as $child) {
      $result = array_merge($result, $this->getRankingSystems($child));
    }
    return $result;
  }
//</editor-fold desc="Private Methods">
}