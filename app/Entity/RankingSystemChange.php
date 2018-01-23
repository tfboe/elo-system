<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/5/18
 * Time: 10:54 PM
 */

namespace App\Entity;


use App\Entity\Helpers\BaseEntity;
use App\Entity\Helpers\SubClassData;
use App\Entity\Helpers\TournamentHierarchyEntity;
use App\Entity\Helpers\UUIDEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class RankingSystemList
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="rankingSystemChanges")
 *
 * Dynamic method hints for Elo ranking
 * @method int getPlayedGames()
 * @method setPlayedGames(int $playedGames)
 * @method int getRatedGames()
 * @method setRatedGames(int $ratedGames)
 * @method float getProvisoryRanking()
 * @method setProvisoryRanking(float $provisoryRanking)
 */
class RankingSystemChange extends BaseEntity
{
  use UUIDEntity;
  use SubClassData;

//<editor-fold desc="Fields">
  /**
   * @ORM\ManyToOne(targetEntity="RankingSystem", inversedBy="changes")
   * @var RankingSystem
   */
  private $rankingSystem;

  /**
   * @ORM\Column(type="float")
   * @var float
   */
  private $pointsChange;

  /**
   * @ORM\Column(type="float")
   * @var float
   */
  private $pointsAfterwards;

  /**
   * @ORM\ManyToOne(targetEntity="Player")
   * @ORM\JoinColumn(referencedColumnName="player_id")
   * @var Player
   */
  private $player;

  /**
   * @ORM\ManyToOne(targetEntity="\App\Entity\Helpers\TournamentHierarchyEntity")
   * @var TournamentHierarchyEntity
   */
  private $hierarchyEntity;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * RankingSystemChange constructor.
   * @param string[] $keys list of additional fields
   */
  public function __construct(array $keys)
  {
    $this->initSubClassData($keys);
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return TournamentHierarchyEntity
   */
  public function getHierarchyEntity(): TournamentHierarchyEntity
  {
    return $this->hierarchyEntity;
  }

  /**
   * @return Player
   */
  public function getPlayer(): Player
  {
    return $this->player;
  }

  /**
   * @return float
   */
  public function getPointsAfterwards(): float
  {
    return $this->pointsAfterwards;
  }

  /**
   * @return float
   */
  public function getPointsChange(): float
  {
    return $this->pointsChange;
  }

  /**
   * @return RankingSystem
   */
  public function getRankingSystem(): RankingSystem
  {
    return $this->rankingSystem;
  }

  /**
   * @param TournamentHierarchyEntity $hierarchyEntity
   * @return $this|RankingSystemChange
   */
  public function setHierarchyEntity(TournamentHierarchyEntity $hierarchyEntity): RankingSystemChange
  {
    $this->hierarchyEntity = $hierarchyEntity;
    return $this;
  }

  /**
   * @param Player $player
   * @return $this|RankingSystemChange
   */
  public function setPlayer(Player $player): RankingSystemChange
  {
    $this->player = $player;
    return $this;
  }

  /**
   * @param float $pointsAfterwards
   * @return $this|RankingSystemChange
   */
  public function setPointsAfterwards(float $pointsAfterwards): RankingSystemChange
  {
    $this->pointsAfterwards = $pointsAfterwards;
    return $this;
  }

  /**
   * @param float $pointsChange
   * @return $this|RankingSystemChange
   */
  public function setPointsChange(float $pointsChange): RankingSystemChange
  {
    $this->pointsChange = $pointsChange;
    return $this;
  }

  /**
   * @param RankingSystem $rankingSystem
   * @return $this|RankingSystemChange
   */
  public function setRankingSystem(RankingSystem $rankingSystem): RankingSystemChange
  {
    $this->rankingSystem = $rankingSystem;
    return $this;
  }
//</editor-fold desc="Public Methods">

}