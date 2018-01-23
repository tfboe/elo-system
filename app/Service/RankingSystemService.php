<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/4/18
 * Time: 4:09 PM
 */

namespace App\Service;


use App\Entity\Helpers\TournamentHierarchyInterface;
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
  private $entityManager;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">

  /**
   * RankingSystemService constructor.
   * @param DynamicServiceLoadingServiceInterface $dsls
   * @param EntityManagerInterface $entityManager
   */
  public function __construct(DynamicServiceLoadingServiceInterface $dsls, EntityManagerInterface $entityManager)
  {
    $this->dsls = $dsls;
    $this->entityManager = $entityManager;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function adaptOpenSyncFromValues(Tournament $tournament, array $oldInfluences): void
  {
    $earliestInfluences = $this->getRankingSystemsEarliestInfluences($tournament);
    foreach ($oldInfluences as $id => $arr) {
      if (array_key_exists($id, $earliestInfluences)) {
        if ($oldInfluences[$id]["earliestInfluence"] < $earliestInfluences[$id]["earliestInfluence"]) {
          $earliestInfluences[$id]["earliestInfluence"] = $oldInfluences[$id]["earliestInfluence"];
        }
      } else {
        $earliestInfluences[$id] = $oldInfluences[$id];
      }
    }
    foreach ($earliestInfluences as $arr) {
      /** @var RankingSystem $ranking */
      $ranking = $arr["rankingSystem"];
      $earliestInfluence = $arr["earliestInfluence"];
      if ($ranking->getOpenSyncFrom() === null || $ranking->getOpenSyncFrom() > $earliestInfluence) {
        $ranking->setOpenSyncFrom($earliestInfluence);
      }
    }
  }

  /**
   * @inheritDoc
   */
  public function applyRankingSystems(Tournament $tournament, array $earliestInfluences): void
  {
    $rankingSystems = $this->getRankingSystems($tournament);
    foreach ($rankingSystems as $sys) {
      if (!array_key_exists($sys->getId(), $earliestInfluences)) {
        $earliestInfluences[$sys->getId()] = [
          "rankingSystem" => $sys,
          "earliestInfluence" => null
        ];
      }
    }
    foreach ($earliestInfluences as $arr) {
      /** @var RankingSystem $ranking */
      $ranking = $arr["rankingSystem"];
      $earliestInfluence = $arr["earliestInfluence"];
      $service = $this->dsls->loadRankingSystemService($ranking->getServiceName());
      $service->updateRankingForTournament($ranking, $tournament, $earliestInfluence);
    }
  }

  /**
   * @inheritDoc
   */
  public function getRankingSystemsEarliestInfluences(Tournament $tournament): array
  {
    $rankingSystems = $this->getRankingSystems($tournament);

    $result = [];
    //compute earliest influences
    foreach ($rankingSystems as $sys) {
      $service = $this->dsls->loadRankingSystemService($sys->getServiceName());
      $result[$sys->getId()] = [
        "rankingSystem" => $sys,
        "earliestInfluence" => $service->getEarliestInfluence($sys, $tournament)
      ];
    }

    return $result;
  }

  /**
   * @inheritDoc
   */
  public function recalculateRankingSystems(): void
  {
    $query = $this->entityManager->createQueryBuilder();
    $query
      ->from(RankingSystem::class, 's')
      ->select('s')
      ->where($query->expr()->isNotNull('s.openSyncFrom'));
    /** @var RankingSystem[] $rankingSystems */
    $rankingSystems = $query->getQuery()->getResult();
    foreach ($rankingSystems as $rankingSystem) {
      $service = $this->dsls->loadRankingSystemService($rankingSystem->getServiceName());
      $service->updateRankingFrom($rankingSystem, $rankingSystem->getOpenSyncFrom());
      $rankingSystem->setOpenSyncFrom(null);
    }
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @param TournamentHierarchyInterface $entity
   * @return RankingSystem[]
   */
  private function getRankingSystems(TournamentHierarchyInterface $entity): array
  {
    $result = $entity->getRankingSystems()->toArray();
    foreach ($entity->getChildren() as $child) {
      $result = array_merge($result, $this->getRankingSystems($child));
    }
    return $result;
  }
//</editor-fold desc="Private Methods">
}