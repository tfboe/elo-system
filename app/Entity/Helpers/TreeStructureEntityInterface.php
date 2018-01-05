<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/4/18
 * Time: 6:33 PM
 */

namespace App\Entity\Helpers;


use App\Entity\RankingSystem;
use Doctrine\Common\Collections\Collection;

/**
 * Interface TreeStructureEntityInterface
 * @package App\Entity\Helpers
 */
interface TreeStructureEntityInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return Collection|TreeStructureEntityInterface[]
   */
  public function getChildren(): Collection;

  /**
   * The end time of the entity
   * @return \DateTime
   */
  public function getEndTime(): ?\DateTime;

  /**
   * Gets the level of the entity (see Level Enum)
   * @return int
   */
  public function getLevel(): int;

  /**
   * @return TreeStructureEntityInterface
   */
  public function getParent(): ?TreeStructureEntityInterface;

  /**
   * @return RankingSystem[]|Collection
   */
  public function getRankingSystems();

  /**
   * The start time of the entity
   * @return \DateTime
   */
  public function getStartTime(): ?\DateTime;
//</editor-fold desc="Public Methods">
}