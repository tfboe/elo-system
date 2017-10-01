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
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Player
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="players")
 */
class Player extends BaseEntity
{
//<editor-fold desc="Fields">
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="SEQUENCE")
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $id;

  /**
   * @ORM\Column(type="string", nullable=false)
   * @var string
   */
  protected $firstName;

  /**
   * @ORM\Column(type="string", nullable=false)
   * @var string
   */
  protected $lastName;

  /**
   * @ORM\Column(type="date", nullable=true)
   * @var \DateTime
   */
  protected $birthday;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return \DateTime
   */
  public function getBirthday(): \DateTime
  {
    $this->ensureNotNull("birthday");
    return $this->birthday;
  }

  /**
   * @return string
   */
  public function getFirstName(): string
  {
    $this->ensureNotNull("firstName");
    return $this->firstName;
  }

  /**
   * @return string
   */
  public function getLastName(): string
  {
    $this->ensureNotNull("lastName");
    return $this->lastName;
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