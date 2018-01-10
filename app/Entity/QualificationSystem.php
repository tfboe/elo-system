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
   * @ORM\ManyToOne(targetEntity="Phase", inversedBy="postQualifications")
   * @var Phase
   */
  protected $previousPhase;

  /**
   * @ORM\ManyToOne(targetEntity="Phase", inversedBy="preQualifications")
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
      $this->nextPhase->getPreQualifications()->removeElement($this);
    }
    $this->nextPhase = $nextPhase;
    $nextPhase->getPreQualifications()->add($this);
    return $this;
  }

  /**
   * @param Phase $previousPhase
   * @return $this|QualificationSystem
   */
  public function setPreviousPhase(Phase $previousPhase): QualificationSystem
  {
    if ($this->previousPhase !== null) {
      $this->previousPhase->getPostQualifications()->removeElement($this);
    }
    $this->previousPhase = $previousPhase;
    $previousPhase->getPostQualifications()->add($this);
    return $this;
  }
//</editor-fold desc="Public Methods">
}