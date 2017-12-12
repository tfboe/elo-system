<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/15/17
 * Time: 10:48 PM
 */

namespace App\Entity;


use App\Entity\Helpers\BaseEntity;
use App\Entity\Helpers\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Contracts\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseEntity implements Authenticatable, JWTSubject
{
  use \LaravelDoctrine\ORM\Auth\Authenticatable;
  use TimestampableEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="CUSTOM")
   * @ORM\CustomIdGenerator(class="App\Entity\Helpers\IdGenerator")
   * @ORM\Column(type="guid")
   * @var string
   */
  protected $id;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  protected $email;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $jwtVersion;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $lastConfirmedAGBVersion;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * User constructor.
   */
  public function __construct()
  {
    $this->jwtVersion = 1;
    $this->lastConfirmedAGBVersion = 0;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return string
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getEmail(): string
  {
    $this->ensureNotNull("email");
    return $this->email;
  }

  /**
   * @return string
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getId(): string
  {
    $this->ensureNotNull("id");
    return $this->id;
  }

  /**
   * Return a key value array, containing any custom claims to be added to the JWT.
   *
   * @return array
   */
  public function getJWTCustomClaims(): array
  {
    return [
      'ver' => $this->jwtVersion
    ];
  }

  /**
   * Get the identifier that will be stored in the subject claim of the JWT.
   *
   * @return string
   * @throws \App\Exceptions\ValueNotSet if the id is not set
   */
  public function getJWTIdentifier(): string
  {
    return $this->getId();
  }

  /**
   * @return int
   */
  public function getJwtVersion(): int
  {
    return $this->jwtVersion;
  }

  /**
   * @return int
   */
  public function getLastConfirmedAGBVersion(): int
  {
    return $this->lastConfirmedAGBVersion;
  }

  /**
   * @param mixed $email
   * @return $this|User
   */
  public function setEmail($email): User
  {
    $this->email = $email;
    return $this;
  }

  /**
   * @param mixed $jwtVersion
   * @return $this|User
   */
  public function setJwtVersion($jwtVersion): User
  {
    $this->jwtVersion = $jwtVersion;
    return $this;
  }

  /**
   * @param mixed $lastConfirmedAGBVersion
   * @return $this|User
   */
  public function setLastConfirmedAGBVersion($lastConfirmedAGBVersion): User
  {
    $this->lastConfirmedAGBVersion = $lastConfirmedAGBVersion;
    return $this;
  }
//</editor-fold desc="Public Methods">
}