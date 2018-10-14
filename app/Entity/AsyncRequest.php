<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/14/18
 * Time: 11:15 AM
 */

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\Timestampable;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\Helpers\TimeEntity;
use Tfboe\FmLib\Entity\Helpers\UUIDEntity;

/**
 * Class AsyncRequest
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="elo_asyncRequests")
 */
class AsyncRequest extends BaseEntity
{
//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="json", nullable=true)
   * @var mixed
   */
  private $result;
  use UUIDEntity;
  use Timestampable;
  use TimeEntity;
  /**
   * @ORM\Column(type="json", nullable=false)
   * @var mixed
   */
  private $input;
  /**
   * @ORM\Column(type="string", nullable=false)
   * @var string
   */
  private $serviceName;
  /**
   * @ORM\Column(type="float", nullable=false)
   * @var float
   */
  private $progress;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * AsyncRequest constructor.
   * @param mixed $input
   * @param string $serviceName
   */
  public function __construct($input, string $serviceName)
  {
    $this->result = null;
    $this->progress = 0.0;
    $this->input = $input;
    $this->serviceName = $serviceName;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return mixed
   */
  public function getInput()
  {
    return $this->input;
  }

  /**
   * @return float
   */
  public function getProgress(): float
  {
    return $this->progress;
  }

  /**
   * @return mixed
   */
  public function getResult()
  {
    return $this->result;
  }

  /**
   * @return string
   */
  public function getServiceName(): string
  {
    return $this->serviceName;
  }

  /**
   * @param float $progress
   */
  public function setProgress(float $progress): void
  {
    $this->progress = $progress;
  }

  /**
   * @param mixed $result
   */
  public function setResult($result): void
  {
    $this->result = $result;
  }
//</editor-fold desc="Public Methods">
}