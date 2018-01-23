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
 * Trait OrganizingMode
 * @package App\Entity\CategoryTraits
 */
trait OrganizingMode
{
//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="smallint", nullable=true)
   * @var int|null
   */
  private $organizingMode;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return int|null
   */
  public function getOrganizingMode(): ?int
  {
    return $this->organizingMode;
  }

  /**
   * @param int|null $organizingMode
   * @return $this|OrganizingMode
   * @throws ValueNotValid
   */
  public function setOrganizingMode(?int $organizingMode)
  {
    if ($organizingMode !== null) {
      \App\Entity\Categories\OrganizingMode::ensureValidValue($organizingMode);
    }
    $this->organizingMode = $organizingMode;
    return $this;
  }
//</editor-fold desc="Public Methods">
}