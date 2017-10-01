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
 * Trait ScoreMode
 * @package App\Entity\CategoryTraits
 */
trait ScoreMode
{
//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="smallint", nullable=true)
   * @var int|null
   */
  protected $scoreMode;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return int|null
   */
  public function getScoreMode(): ?int
  {
    return $this->scoreMode;
  }

  /**
   * @param int|null $scoreMode
   * @return $this|ScoreMode
   * @throws ValueNotValid
   */
  public function setScoreMode(?int $scoreMode)
  {
    \App\Entity\Categories\ScoreMode::ensureValidValue($scoreMode);
    $this->scoreMode = $scoreMode;
    return $this;
  }
//</editor-fold desc="Public Methods">
}