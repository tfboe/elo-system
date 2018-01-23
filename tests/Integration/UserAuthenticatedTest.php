<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/16/17
 * Time: 2:04 PM
 */

namespace Tests\Integration;

use App\Entity\User;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\AuthenticatedTestCase;

/**
 * Class UserAuthenticatedTest
 * @package Tests\Integration
 */
class UserAuthenticatedTest extends AuthenticatedTestCase
{
//<editor-fold desc="Public Methods">
  public function testInvalidateToken()
  {
    /** @var User $user */
    /** @noinspection PhpUndefinedMethodInspection */
    $user = EntityManager::find(User::class, $this->user->getId());
    $user->setJwtVersion(2);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::flush();
    $this->jsonAuth('GET', '/userId')->seeStatusCode(401);
    self::assertNull($this->response->headers->get('jwt-token'));
  }

  public function testUserId()
  {
    $this->jsonAuth('GET', '/userId')->seeJsonEquals(['id' => $this->user->getId()]);
  }
//</editor-fold desc="Public Methods">
}