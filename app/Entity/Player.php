<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 10:27 AM
 */

namespace App\Entity;


use App\Entity\Helpers\BaseEntity;
use App\Entity\Helpers\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Player
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="players",indexes={@ORM\Index(name="unique_names_birthday",
 *   columns={"first_name","last_name","birthday"})})
 */
class Player extends BaseEntity
{
  use TimestampableEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer")
   * @var int
   */
  private $playerId;

  /**
   * @ORM\Column(type="string", nullable=false)
   * @var string
   */
  private $firstName;

  /**
   * @ORM\Column(type="string", nullable=false)
   * @var string
   */
  private $lastName;

  /**
   * @ORM\Column(type="date", nullable=true)
   * @var \DateTime
   */
  private $birthday;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return \DateTime
   */
  public function getBirthday(): \DateTime
  {
    return $this->birthday;
  }

  /**
   * @return string
   */
  public function getFirstName(): string
  {
    return $this->firstName;
  }

  /**
   * @return string
   */
  public function getLastName(): string
  {
    return $this->lastName;
  }

  /**
   * @return int
   */
  public function getPlayerId(): int
  {
    return $this->playerId;
  }

  /**
   * @param \DateTime $birthday
   * @return $this|Player
   */
  public function setBirthday(\DateTime $birthday): Player
  {
    $this->birthday = $birthday;
    return $this;
  }

  /**
   * @param string $firstName
   * @return $this|Player
   */
  public function setFirstName(string $firstName): Player
  {
    $this->firstName = $firstName;
    return $this;
  }

  /**
   * @param string $lastName
   * @return $this|Player
   */
  public function setLastName(string $lastName): Player
  {
    $this->lastName = $lastName;
    return $this;
  }
//</editor-fold desc="Public Methods">
}