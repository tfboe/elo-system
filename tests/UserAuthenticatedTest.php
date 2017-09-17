<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/16/17
 * Time: 2:04 PM
 */

class UserAuthenticatedTest extends AuthenticatedTestCase
{
//<editor-fold desc="Public Methods">
  public function testUserId()
  {
    $this->jsonAuth('GET', '/userId')->seeJsonEquals(['id' => $this->user->getId()]);
  }

  public function testInvalidateToken()
  {
    /** @var \App\Entity\User $user */
    /** @noinspection PhpUndefinedMethodInspection */
    $user = \LaravelDoctrine\ORM\Facades\EntityManager::find(\App\Entity\User::class, $this->user->getId());
    $user->setJwtVersion(2);
    /** @noinspection PhpUndefinedMethodInspection */
    \LaravelDoctrine\ORM\Facades\EntityManager::flush();
    $this->jsonAuth('GET', '/userId')->seeStatusCode(401);
    self::assertNull($this->response->headers->get('jwt-token'));
  }
//</editor-fold desc="Public Methods">
}