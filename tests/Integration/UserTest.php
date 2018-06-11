<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/11/18
 * Time: 8:11 AM
 */

namespace Tests\Integration;

/**
 * Class UserTest
 * @package Tests\Integration
 */
class UserTest extends AuthenticatedTestCase
{
//<editor-fold desc="Public Methods">
  public function testRegisterUser()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com',
      'password' => 'testPassword'
    ])->seeJsonStructure(['id'])->seeHeader('jwt-token');
    self::assertNotNull($this->response->headers->get('jwt-token'));
  }
//</editor-fold desc="Public Methods">
}