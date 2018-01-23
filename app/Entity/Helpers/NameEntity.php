<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 11:02 AM
 */

namespace App\Entity\Helpers;

/**
 * Trait NameEntity
 * @package App\Entity\Helpers
 */
trait NameEntity
{
//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $name;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * @param string $name
   * @return $this|NameEntity
   */
  public function setName(string $name)
  {
    $this->name = $name;
    return $this;
  }
//</editor-fold desc="Public Methods">
}