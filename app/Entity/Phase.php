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
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Phase
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="phases")
 */
class Phase extends BaseEntity
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
   * @ORM\ManyToOne(targetEntity="Competition", inversedBy="phases")
   * @var Competition
   */
  protected $competition;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $phaseNumber;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  protected $name;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * Competition constructor.
   */
  public function __construct()
  {
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return Competition
   */
  public function getCompetition(): Competition
  {
    $this->ensureNotNull('competition');
    return $this->competition;
  }

  /**
   * @return string
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
    $this->ensureNotNull('name');
    return $this->name;
  }

  /**
   * @return int
   */
  public function getPhaseNumber(): int
  {
    $this->ensureNotNull('phaseNumber');
    return $this->phaseNumber;
  }

  /**
   * @param Competition $competition
   * @return $this|Phase
   */
  public function setCompetition(Competition $competition): Phase
  {
    $this->competition = $competition;
    $competition->getPhases()->set($this->getPhaseNumber(), $this);
    return $this;
  }

  /**
   * @param string $name
   * @return $this|Phase
   */
  public function setName(string $name): Phase
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @param int $phaseNumber
   * @return $this|Phase
   */
  public function setPhaseNumber(int $phaseNumber): Phase
  {
    $this->phaseNumber = $phaseNumber;
    if (!isset($this->name)) {
      $this->setName("Phase " . $phaseNumber);
    }
    return $this;
  }
//</editor-fold desc="Public Methods">
}