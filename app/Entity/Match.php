<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 12/4/17
 * Time: 10:49 PM
 */

namespace App\Entity;

use App\Entity\CategoryTraits\GameMode;
use App\Entity\CategoryTraits\OrganizingMode;
use App\Entity\CategoryTraits\ScoreMode;
use App\Entity\CategoryTraits\Table;
use App\Entity\CategoryTraits\TeamMode;
use App\Entity\Helpers\BaseEntity;
use App\Entity\Helpers\ResultEntity;
use App\Entity\Helpers\TimeEntity;
use App\Entity\Helpers\TreeStructureEntityInterface;
use App\Entity\Helpers\UUIDEntity;
use App\Helpers\Level;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Phase
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="matches")
 */
class Match extends BaseEntity implements TreeStructureEntityInterface
{
  use GameMode;
  use TeamMode;
  use OrganizingMode;
  use ScoreMode;
  use Table;
  use ResultEntity;
  use TimeEntity;
  use UUIDEntity;

//<editor-fold desc="Fields">

  /**
   * @ORM\ManyToOne(targetEntity="Phase", inversedBy="matches")
   * @var Phase
   */
  protected $phase;

  /**
   * @ORM\ManyToMany(targetEntity="Ranking", indexBy="uniqueRank")
   * @ORM\JoinTable(name="relation__match_rankingA")
   * @var Collection|Ranking
   */
  protected $rankingsA;

  /**
   * @ORM\ManyToMany(targetEntity="Ranking", indexBy="uniqueRank")
   * @ORM\JoinTable(name="relation__match_rankingB")
   * @var Collection|Ranking
   */
  protected $rankingsB;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $matchNumber;

  /**
   * @ORM\OneToMany(targetEntity="Game", mappedBy="match", indexBy="gameNumber")
   * @var Collection|Game[]
   */
  protected $games;

  /**
   * @ORM\ManyToMany(
   *     targetEntity="RankingSystem",
   *     inversedBy="matches",
   *     indexBy="id"
   * )
   * @ORM\JoinTable(name="relation__match_ranking_systems")
   * @var Collection|RankingSystem[]
   */
  private $rankingSystems;

  /**
   * @return RankingSystem[]|Collection
   */
  public function getRankingSystems()
  {
    return $this->rankingSystems;
  }
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * Match constructor.
   */
  public function __construct()
  {
    $this->rankingsA = new ArrayCollection();
    $this->rankingsB = new ArrayCollection();
    $this->games = new ArrayCollection();
    $this->rankingSystems = new ArrayCollection();
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">


  /**
   * @return Game[]|Collection
   */
  public function getGames()
  {
    return $this->games;
  }

  /**
   * @inheritDoc
   */
  public function getLocalIdentifier()
  {
    return $this->getMatchNumber();
  }

  /**
   * @return int
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getMatchNumber(): int
  {
    $this->ensureNotNull('matchNumber');
    return $this->matchNumber;
  }

  /**
   * @return Phase
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getPhase(): Phase
  {
    $this->ensureNotNull('phase');
    return $this->phase;
  }

  /**
   * @return Ranking|Collection
   */
  public function getRankingsA()
  {
    return $this->rankingsA;
  }

  /**
   * @return Ranking|Collection
   */
  public function getRankingsB()
  {
    return $this->rankingsB;
  }


  /**
   * @param int $matchNumber
   * @return $this|Match
   */
  public function setMatchNumber(int $matchNumber): Match
  {
    $this->matchNumber = $matchNumber;
    return $this;
  }

  /**
   * @param Phase $phase
   * @return $this|Match
   * @throws \App\Exceptions\ValueNotSet the match number is not yet set
   */
  public function setPhase(Phase $phase): Match
  {
    if ($this->phase !== null) {
      $this->phase->getMatches()->remove($this->getMatchNumber());
    }
    $this->phase = $phase;
    $phase->getMatches()->set($this->getMatchNumber(), $this);
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getParent(): ?TreeStructureEntityInterface
  {
    return $this->phase;
  }

  /**
   * @inheritDoc
   */
  public function getChildren(): Collection
  {
    return $this->getGames();
  }

  /**
   * @inheritDoc
   */
  public function getLevel(): int
  {
    return Level::MATCH;
  }
//</editor-fold desc="Public Methods">
}