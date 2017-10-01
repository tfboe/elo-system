<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/16/17
 * Time: 2:04 PM
 */

namespace Tests\Helpers;

use App\Entity\User;
use Doctrine\DBAL\Connection;
use LaravelDoctrine\ORM\Facades\EntityManager;

class AuthenticatedTestCase extends DatabaseTestCase
{
//<editor-fold desc="Fields">
  /**
   * Authentication token if logged in
   * @var string
   */
  protected $token;

  /**
   * User corresponding to authentication token if logged in
   * @var \App\Entity\User
   */
  protected $user;
//</editor-fold desc="Fields">

//<editor-fold desc="Protected Methods">
  /**
   * sends a json request with an authentication token
   * @param string $method the method to use (GET, POST, ...)
   * @param string $uri the uri of the request
   * @param array $data the post data
   * @param array $headers the request headers
   * @return $this
   */
  protected function jsonAuth(string $method, string $uri, array $data = [], array $headers = [])
  {
    $headers['Authorization'] = 'Bearer ' . $this->token;
    return $this->json($method, $uri, $data, $headers);
  }

  protected function workOnDatabaseDestroy()
  {
    /** @var Connection $connection */
    /** @noinspection PhpUndefinedMethodInspection */
    $connection = EntityManager::getConnection();
    $sql = sprintf('SET FOREIGN_KEY_CHECKS=0;TRUNCATE TABLE %s;SET FOREIGN_KEY_CHECKS=1;', "users");
    $connection->query($sql);
    parent::workOnDatabaseDestroy();
  }

  protected function workOnDatabaseSetUp()
  {
    parent::workOnDatabaseSetUp();
    $password = $this->newPassword();
    $this->user = entity(User::class)->create(['originalPassword' => $password]);
    $this->token = \Auth::attempt(['email' => $this->user->getEmail(), 'password' => $password]);
    $this->refreshApplication();
  }
//</editor-fold desc="Protected Methods">
}