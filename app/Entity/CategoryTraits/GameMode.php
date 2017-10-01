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
 * Trait GameMode
 * @package App\Entity\CategoryTraits
 */
trait GameMode
{
//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="smallint", nullable=true)
   * @var int|null
   */
  protected $gameMode;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return int|null
   */
  public function getGameMode(): ?int
  {
    return $this->gameMode;
  }

  /**
   * @param int|null $gameMode
   * @return $this|GameMode
   * @throws ValueNotValid
   */
  public function setGameMode(?int $gameMode)
  {
    \App\Entity\Categories\GameMode::ensureValidValue($gameMode);
    $this->gameMode = $gameMode;
    return $this;
  }
//</editor-fold desc="Public Methods">
}