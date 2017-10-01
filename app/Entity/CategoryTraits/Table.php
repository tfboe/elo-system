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
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait Table
 * @package App\Entity\CategoryTraits
 */
trait Table
{
//<editor-fold desc="Fields">
  /**
   * @ORM\Column(name="table_id", type="smallint", nullable=true)
   * @var int|null
   */
  protected $table;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return int|null
   */
  public function getTable(): ?int
  {
    return $this->table;
  }

  /**
   * @param int|null $table
   * @return $this|Table
   * @throws ValueNotValid
   */
  public function setTable(?int $table)
  {
    \App\Entity\Categories\Table::ensureValidValue($table);
    $this->table = $table;
    return $this;
  }
//</editor-fold desc="Public Methods">
}