<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/6/16
 * Time: 11:18 AM
 */

namespace App\Entity\Helpers;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Doctrine\ORM\Mapping\Entity;

/**
 * Class IdGenerator. Generator for unique ids.
 * @package App\Entity
 */
class IdGenerator extends AbstractIdGenerator
{
//<editor-fold desc="Public Methods">
  /**
   * Generates an identifier for an entity.
   *
   * @param EntityManager|EntityManager $em
   * @param Entity $entity
   * @return string
   */
  public function generate(EntityManager $em, $entity): string
  {
    return self::createIdFrom();
  }

  /**
   * creates a new id
   * @param string $creator_function the id creator function name to use (if existent)
   * @return string the new id
   */
  public static function createIdFrom($creator_function = 'com_create_guid')
  {
    if (function_exists($creator_function) === true) {
      return strtolower(trim($creator_function(), '{}'));
    }

    return strtolower(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
      mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151),
      mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));
  }
//</editor-fold desc="Public Methods">

}