<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/7/18
 * Time: 9:04 PM
 */

namespace App\Service\RankingSystem;


use App\Entity\Helpers\TreeStructureEntityInterface;
use App\Entity\Tournament;

/**
 * Class RecursiveEndStartTimeService
 * @package App\Service\RankingSystem
 */
class RecursiveEndStartTimeService implements TimeServiceInterface
{
//<editor-fold desc="Fields">
  /** @var \DateTime[] */
  private $times = [];
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function clearTimes()
  {
    $this->times = [];
  }

  /**
   * @inheritDoc
   */
  public function getTime(TreeStructureEntityInterface $entity)
  {
    $entityId = $entity->getId();
    if (!array_key_exists($entityId, $this->times)) {
      $this->times[$entityId] = $entity->getEndTime();
      if ($this->times[$entityId] == null) {
        $this->times[$entityId] = $entity->getStartTime();
        if ($this->times[$entityId] == null) {
          if ($entity->getParent() !== null) {
            $this->times[$entityId] = $this->getTime($entity->getParent());
          } else {
            //entity must be a tournament
            /** @var Tournament $entity */
            $this->times[$entityId] = $entity->getUpdatedAt();
          }
        }
      }
    }
    return $this->times[$entityId];
  }
//</editor-fold desc="Public Methods">
}