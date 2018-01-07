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
use App\Entity\Helpers\UUIDEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class RankingSystemList
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="rankingSystemLists")
 */
class RankingSystemList extends BaseEntity
{
  use UUIDEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\ManyToOne(targetEntity="RankingSystem", inversedBy="lists")
   * @var RankingSystem
   */
  protected $rankingSystem;
  /**
   * @ORM\Column(type="boolean")
   * @var bool
   */
  private $current;
  /**
   * @ORM\Column(type="datetime")
   * @var \DateTime
   */
  private $lastEntryTime;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * RankingSystemList constructor.
   */
  public function __construct()
  {
    $this->lastEntryTime = new \DateTime("2000-01-01");
    $this->current = false;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return \DateTime
   */
  public function getLastEntryTime(): \DateTime
  {
    return $this->lastEntryTime;
  }

  /**
   * @return RankingSystem
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getRankingSystem(): RankingSystem
  {
    $this->ensureNotNull('rankingSystem');
    return $this->rankingSystem;
  }

  /**
   * @return bool
   */
  public function isCurrent(): bool
  {
    return $this->current;
  }

  /**
   * @param bool $current
   * @return $this|RankingSystemList
   */
  public function setCurrent(bool $current): RankingSystemList
  {
    $this->current = $current;
    return $this;
  }

  /**
   * @param \DateTime $lastEntryTime
   * @return $this|RankingSystemList
   */
  public function setLastEntryTime(\DateTime $lastEntryTime): RankingSystemList
  {
    $this->lastEntryTime = $lastEntryTime;
    return $this;
  }

  /**
   * @param RankingSystem $rankingSystem
   * @return $this|RankingSystemList
   * @throws \App\Exceptions\ValueNotSet if the id is not set
   */
  public function setRankingSystem(RankingSystem $rankingSystem): RankingSystemList
  {
    if ($this->rankingSystem !== null) {
      $this->rankingSystem->getLists()->remove($this->getId());
    }
    $this->rankingSystem = $rankingSystem;
    $rankingSystem->getLists()->set($this->getId(), $this);

    return $this;
  }
//</editor-fold desc="Public Methods">
}