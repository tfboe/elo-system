<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 10:27 AM
 */

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\Helpers\NumericalId;
use Tfboe\FmLib\Entity\PlayerInterface;

/**
 * Class Player
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="elo_players",indexes={
 *   @ORM\Index(name="unique_names_birthday",columns={"first_name","last_name","birthday"}),
 *   @ORM\Index(name="itsf_license_number",columns={"itsf_license_number"})})
 */
class Player extends BaseEntity implements PlayerInterface
{
  use \Tfboe\FmLib\Entity\Traits\Player;
  use NumericalId;

  /**
   * @ORM\OneToMany(targetEntity="\App\Entity\Player", indexBy="id", mappedBy="mergedInto")
   * @var ArrayCollection|Player[]
   */
  private $mergedPlayers;


  /**
   * @ORM\ManyToOne(targetEntity="\App\Entity\Player", inversedBy="mergedPlayers")
   * @var Player|null
   */
  private $mergedInto;

  /**
   * @ORM\Column(type="integer", nullable=true)
   * @var int|null
   */
  private $itsfLicenseNumber;

  /**
   * Player constructor.
   */
  public function __construct()
  {
    $this->mergedPlayers = new ArrayCollection();
  }

  public function __clone()
  {
      $this->mergedPlayers = new ArrayCollection();
      $this->mergedInto = null;
      $this->id = null;
      $this->createdAt = null;
      $this->updatedAt = null;
  }

  /**
   * @return Player[]|ArrayCollection
   */
  public function getMergedPlayers()
  {
    return $this->mergedPlayers;
  }

  /**
   * @return Player|null
   */
  public function getMergedInto(): ?Player
  {
    return $this->mergedInto;
  }

  /**
   * @return int|null
   */
  public function getItsfLicenseNumber(): ?int
  {
    return $this->itsfLicenseNumber;
  }

  /**
   * @param int|null $itsfLicenseNumber
   * @return $this|Player
   */
  public function setItsfLicenseNumber(?int $itsfLicenseNumber): Player
  {
    $this->itsfLicenseNumber = $itsfLicenseNumber;
    return $this;
  }

  /**
   * @param Player|null $mergedInto
   * @return $this|Player
   */
  public function setMergedInto(?Player $mergedInto): Player
  {
    if ($this->mergedInto !== null) {
      $this->mergedInto->getMergedPlayers()->remove($this->getId());
    }
    $this->mergedInto = $mergedInto;
    $mergedInto->getMergedPlayers()->set($this->getId(), $this);
    return $this;
  }

  /**
   * Gets the currently merge base player for this player, i.e. itself it didn't get merged into another player.
   * @return Player
   */
  public function getPlayer(): Player
  {
    if ($this->mergedInto !== null) {
      return $this->mergedInto->getPlayer();
    } else {
      return $this;
    }
  }
//</editor-fold desc="Public Methods">
}