<?php

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
    if (function_exists('com_create_guid') === true) {
      return strtolower(trim(com_create_guid(), '{}'));
    }

    return strtolower(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
      mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151),
      mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));
  }
//</editor-fold desc="Public Methods">

}