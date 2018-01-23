<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 10:57 AM
 */

namespace App\Entity\Helpers;

/**
 * Trait UUIDEntity
 * @package App\Entity\Helpers
 */
trait UUIDEntity
{
//<editor-fold desc="Fields">
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="CUSTOM")
   * @ORM\CustomIdGenerator(class="App\Entity\Helpers\IdGenerator")
   * @ORM\Column(type="guid")
   * @var string
   */
  private $id;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return string
   */
  public function getId(): string
  {
    return $this->id;
  }
//</editor-fold desc="Public Methods">
}