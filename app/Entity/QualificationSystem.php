<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 12/3/17
 * Time: 5:55 PM
 */

namespace App\Entity;


use App\Entity\Helpers\BaseEntity;
use App\Entity\Helpers\UUIDEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class QualificationSystem
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="qualificationSystems")
 */
class QualificationSystem extends BaseEntity
{
  use UUIDEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\ManyToOne(targetEntity="Phase", inversedBy="nextQualificationSystems")
   * @var Phase
   */
  protected $previousPhase;

  /**
   * @ORM\ManyToOne(targetEntity="Phase", inversedBy="previousQualificationSystems")
   * @var Phase
   */
  protected $nextPhase;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return Phase
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getNextPhase(): Phase
  {
    $this->ensureNotNull("nextPhase");
    return $this->nextPhase;
  }

  /**
   * @return Phase
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getPreviousPhase(): Phase
  {
    $this->ensureNotNull("previousPhase");
    return $this->previousPhase;
  }

  /**
   * @param Phase $nextPhase
   * @return $this|QualificationSystem
   */
  public function setNextPhase(Phase $nextPhase): QualificationSystem
  {
    if ($this->nextPhase !== null) {
      $this->nextPhase->getPreviousQualificationSystems()->removeElement($this);
    }
    $this->nextPhase = $nextPhase;
    $nextPhase->getPreviousQualificationSystems()->add($this);
    return $this;
  }

  /**
   * @param Phase $previousPhase
   * @return $this|QualificationSystem
   */
  public function setPreviousPhase(Phase $previousPhase): QualificationSystem
  {
    if ($this->previousPhase !== null) {
      $this->previousPhase->getNextQualificationSystems()->removeElement($this);
    }
    $this->previousPhase = $previousPhase;
    $previousPhase->getNextQualificationSystems()->add($this);
    return $this;
  }
//</editor-fold desc="Public Methods">
}