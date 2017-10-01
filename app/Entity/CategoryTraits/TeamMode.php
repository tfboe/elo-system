<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/22/17
 * Time: 5:38 PM
 */

namespace App\Entity\CategoryTraits;

use App\Exceptions\ValueNotValid;

/**
 * Trait TeamMode
 * @package App\Entity\CategoryTraits
 */
trait TeamMode
{
//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="smallint", nullable=true)
   * @var int|null
   */
  protected $teamMode;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return int|null
   */
  public function getTeamMode(): ?int
  {
    return $this->teamMode;
  }

  /**
   * @param int|null $teamMode
   * @return $this|TeamMode
   * @throws ValueNotValid
   */
  public function setTeamMode(?int $teamMode)
  {
    \App\Entity\Categories\TeamMode::ensureValidValue($teamMode);
    $this->teamMode = $teamMode;
    return $this;
  }
//</editor-fold desc="Public Methods">
}