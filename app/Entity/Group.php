<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/15/17
 * Time: 10:57 AM
 */

namespace App\Entity;

use App\Entity\CategoryTraits\GameMode;
use App\Entity\CategoryTraits\OrganizingMode;
use App\Entity\CategoryTraits\ScoreMode;
use App\Entity\CategoryTraits\Table;
use App\Entity\CategoryTraits\TeamMode;
use App\Entity\Helpers\BaseEntity;
use App\Exceptions\ValueNotSet;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Group
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="groups")
 */
class Group extends BaseEntity
{
  use GameMode;
  use TeamMode;
  use OrganizingMode;
  use ScoreMode;
  use Table;

//<editor-fold desc="Fields">
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="CUSTOM")
   * @ORM\CustomIdGenerator(class="App\Entity\Helpers\IdGenerator")
   * @ORM\Column(type="guid")
   * @var string
   */
  protected $id;

  /**
   * @ORM\ManyToOne(targetEntity="Phase", inversedBy="groups")
   * @var Phase
   */
  protected $phase;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $groupNumber;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  protected $name;

  /**
   * @ORM\OneToMany(targetEntity="Ranking", mappedBy="group", indexBy="uniqueRank")
   * @var Collection|Ranking[]
   */
  protected $rankings;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * Competition constructor.
   */
  public function __construct()
  {
    $this->name = '';
    $this->rankings = new ArrayCollection();
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return int
   * @throws ValueNotSet
   */
  public function getGroupNumber(): int
  {
    $this->ensureNotNull('groupNumber');
    return $this->groupNumber;
  }

  /**
   * @return string
   * @throws ValueNotSet
   */
  public function getId(): string
  {
    $this->ensureNotNull('id');
    return $this->id;
  }

  /**
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * @return Phase
   * @throws ValueNotSet
   */
  public function getPhase(): Phase
  {
    $this->ensureNotNull('phase');
    return $this->phase;
  }

  /**
   * @return Ranking[]|Collection
   */
  public function getRankings()
  {
    return $this->rankings;
  }

  /**
   * @param int $groupNumber
   * @return $this|Group
   */
  public function setGroupNumber(int $groupNumber): Group
  {
    $this->groupNumber = $groupNumber;
    return $this;
  }

  /**
   * @param string $name
   * @return $this|Group
   */
  public function setName(string $name): Group
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @param Phase $phase
   * @return $this|Group
   * @throws ValueNotSet if the group number is not set
   */
  public function setPhase(Phase $phase): Group
  {
    $this->phase = $phase;
    $phase->getGroups()->set($this->getGroupNumber(), $this);
    return $this;
  }
//</editor-fold desc="Public Methods">
}