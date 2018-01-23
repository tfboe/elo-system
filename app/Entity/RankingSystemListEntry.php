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
use App\Entity\Helpers\UUIDEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class RankingSystemList
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="rankingSystemListEntry")
 *
 *
 * Dynamic method hints for Elo ranking
 * @method int getPlayedGames()
 * @method RankingSystemListEntry setPlayedGames(int $playedGames)
 * @method int getRatedGames()
 * @method RankingSystemListEntry setRatedGames(int $ratedGames)
 * @method float getProvisoryRanking()
 * @method RankingSystemListEntry setProvisoryRanking(float $provisoryRanking)
 */
class RankingSystemListEntry extends BaseEntity
{
  use UUIDEntity;
  use SubClassData;

//<editor-fold desc="Fields">
  /**
   * @ORM\ManyToOne(targetEntity="RankingSystemList", inversedBy="entries")
   * @var RankingSystemList
   */
  protected $rankingSystemList;

  /**
   * @ORM\Column(type="float")
   * @var float
   */
  protected $points;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $numberRankedEntities;

  /**
   * @ORM\ManyToOne(targetEntity="Player")
   * @ORM\JoinColumn(referencedColumnName="player_id")
   * @var Player
   */
  protected $player;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * RankingSystemListEntry constructor.
   * @param string[] $keys list of additional fields
   */
  public function __construct(array $keys)
  {
    $this->numberRankedEntities = 0;
    $this->initSubClassData($keys);
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return int
   */
  public function getNumberRankedEntities(): int
  {
    return $this->numberRankedEntities;
  }

  /**
   * @return Player
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getPlayer(): Player
  {
    $this->ensureNotNull('player');
    return $this->player;
  }

  /**
   * @return float
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getPoints(): float
  {
    $this->ensureNotNull('points');
    return $this->points;
  }

  /**
   * @return RankingSystemList
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getRankingSystemList(): RankingSystemList
  {
    $this->ensureNotNull('rankingSystemList');
    return $this->rankingSystemList;
  }

  /**
   * @param int $numberRankedEntities
   * @return $this|RankingSystemListEntry
   */
  public function setNumberRankedEntities(int $numberRankedEntities): RankingSystemListEntry
  {
    $this->numberRankedEntities = $numberRankedEntities;
    return $this;
  }

  /**
   * @param Player $player
   * @return $this|RankingSystemListEntry
   */
  public function setPlayer(Player $player): RankingSystemListEntry
  {
    $this->player = $player;
    return $this;
  }

  /**
   * @param float $points
   * @return $this|RankingSystemListEntry
   */
  public function setPoints(float $points): RankingSystemListEntry
  {
    $this->points = $points;
    return $this;
  }

  /**
   * @param RankingSystemList $rankingSystemList
   * @return $this|RankingSystemListEntry
   * @throws \App\Exceptions\ValueNotSet player not set or its player_id is not set
   */
  public function setRankingSystemList(RankingSystemList $rankingSystemList): RankingSystemListEntry
  {
    if ($this->rankingSystemList !== null) {
      $this->rankingSystemList->getEntries()->remove($this->getPlayer()->getPlayerId());
    }
    $this->rankingSystemList = $rankingSystemList;
    $rankingSystemList->getEntries()->set($this->getPlayer()->getPlayerId(), $this);
    return $this;
  }
//</editor-fold desc="Public Methods">


}