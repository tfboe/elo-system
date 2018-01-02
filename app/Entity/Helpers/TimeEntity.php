<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 2:57 PM
 */

namespace App\Entity\Helpers;

/**
 * Trait TimeEntity
 * @package App\Entity\Helpers
 */
trait TimeEntity
{
//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="datetimetz", nullable=true)
   * @var ?\DateTime
   */
  protected $startTime = null;

  /**
   * @ORM\Column(type="datetimetz", nullable=true)
   * @var ?\DateTime
   */
  protected $endTime = null;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return \DateTime|null
   */
  public function getEndTime(): ?\DateTime
  {
    return $this->endTime;
  }

  /**
   * @return \DateTime|null
   */
  public function getStartTime(): ?\DateTime
  {
    return $this->startTime;
  }


  /**
   * @param \DateTime|null $endTime
   * @return $this|TimeEntity
   */
  public function setEndTime(?\DateTime $endTime)
  {
    $this->endTime = $endTime;
    return $this;
  }

  /**
   * @param \DateTime|null $startTime
   * @return $this|TimeEntity
   */
  public function setStartTime(?\DateTime $startTime)
  {
    $this->startTime = $startTime;
    return $this;
  }
//</editor-fold desc="Public Methods">
}