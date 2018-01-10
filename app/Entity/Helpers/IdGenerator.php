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
   * creates a new id
   * @param string $creatorFunction the id creator function name to use (if existent)
   * @return string the new id
   */
  public static function createIdFrom($creatorFunction = 'com_create_guid')
  {
    if (function_exists($creatorFunction) === true) {
      return strtolower(trim($creatorFunction(), '{}'));
    }

    return strtolower(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
      mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151),
      mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));
  }

  /**
   * Generates an identifier for an entity.
   *
   * @param EntityManager $entityManager
   * @param Entity $entity
   * @return string
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function generate(EntityManager $entityManager, $entity): string
  {
    return self::createIdFrom();
  }
//</editor-fold desc="Public Methods">

}