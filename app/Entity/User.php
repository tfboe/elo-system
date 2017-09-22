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
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return mixed
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * @return string
   */
  public function getId(): string
  {
    return $this->id;
  }

  /**
   * Return a key value array, containing any custom claims to be added to the JWT.
   *
   * @return array
   */
  public function getJWTCustomClaims()
  {
    return [
      'ver' => $this->jwtVersion
    ];
  }

  /**
   * Get the identifier that will be stored in the subject claim of the JWT.
   *
   * @return mixed
   */
  public function getJWTIdentifier()
  {
    return $this->getId();
  }

  /**
   * @return mixed
   */
  public function getJwtVersion()
  {
    return $this->jwtVersion;
  }

  /**
   * @return mixed
   */
  public function getLastConfirmedAGBVersion()
  {
    return $this->lastConfirmedAGBVersion;
  }

  /**
   * @param mixed $email
   * @return $this|User
   */
  public function setEmail($email)
  {
    $this->email = $email;
    return $this;
  }

  /**
   * @param mixed $jwtVersion
   * @return $this|User
   */
  public function setJwtVersion($jwtVersion)
  {
    $this->jwtVersion = $jwtVersion;
    return $this;
  }

  /**
   * @param mixed $lastConfirmedAGBVersion
   * @return $this|User
   */
  public function setLastConfirmedAGBVersion($lastConfirmedAGBVersion)
  {
    $this->lastConfirmedAGBVersion = $lastConfirmedAGBVersion;
    return $this;
  }
//</editor-fold desc="Public Methods">
}