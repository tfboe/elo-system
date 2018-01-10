<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/7/18
 * Time: 9:17 PM
 */

namespace App\Service\RankingSystem;


use App\Entity\Helpers\TreeStructureEntityInterface;

/**
 * Interface EntityComparerInterface
 * @package App\Service\RankingSystem
 */
interface EntityComparerInterface
{
//<editor-fold desc="Public Methods">
  /**
   * Compares two entities.
   * @param TreeStructureEntityInterface $entity1 the first entity to compare
   * @param TreeStructureEntityInterface $entity2 the second entity to compare
   * @return int returns -1 if entity1 should be before entity2, 1 if it should be after entity2 and 0 if they are
   *             equal.
   */
  public function compareEntities(TreeStructureEntityInterface $entity1, TreeStructureEntityInterface $entity2): int;
//</editor-fold desc="Public Methods">
}