<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 12/16/17
 * Time: 1:04 PM
 */

namespace App\Entity\Helpers;


use App\Exceptions\ValueNotValid;

/**
 * Trait ResultEntity
 * @package App\Entity\Helpers
 */
trait ResultEntity
{
  use UnsetProperty;

//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $resultA;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $resultB;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $result;

  /**
   * @ORM\Column(type="boolean")
   * @var bool
   */
  protected $played;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return int
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getResult(): int
  {
    $this->ensureNotNull('result');
    return $this->result;
  }

  /**
   * @return int
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getResultA(): int
  {
    $this->ensureNotNull('resultA');
    return $this->resultA;
  }

  /**
   * @return int
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getResultB(): int
  {
    $this->ensureNotNull('resultB');
    return $this->resultB;
  }

  /**
   * @return bool
   * @throws \App\Exceptions\ValueNotSet
   */
  public function isPlayed(): bool
  {
    $this->ensureNotNull('played');
    return $this->played;
  }

  /**
   * @param bool $played
   * @return $this|ResultEntity
   */
  public function setPlayed(bool $played)
  {
    $this->played = $played;
    return $this;
  }

  /**
   * @param int $result
   * @return $this|ResultEntity
   * @throws ValueNotValid
   */
  public function setResult(int $result)
  {
    Result::ensureValidValue($result);
    $this->result = $result;
    return $this;
  }

  /**
   * @param int $resultA
   * @return $this|ResultEntity
   */
  public function setResultA(int $resultA)
  {
    $this->resultA = $resultA;
    return $this;
  }

  /**
   * @param int $resultB
   * @return $this|ResultEntity
   */
  public function setResultB(int $resultB)
  {
    $this->resultB = $resultB;
    return $this;
  }
//</editor-fold desc="Public Methods">
}