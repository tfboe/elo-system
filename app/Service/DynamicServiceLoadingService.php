<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/17
 * Time: 7:29 PM
 */

namespace App\Service;

use App\Service\RankingSystem\RankingSystemInterface;

/**
 * Class DynamicLoadingService
 * @package App\Service
 */
class DynamicServiceLoadingService implements DynamicServiceLoadingServiceInterface
{
//<editor-fold desc="Public Methods">

  /**
   * @inheritdoc
   */
  public function loadRankingSystemService(string $name): RankingSystemInterface
  {
    return app($this->getClassWithNamespace($name, 'App\Service\RankingSystem'));
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * Gets the full name of the given class with respect to the given namespace
   * @param string $class the class name
   * @param string $namespace the namespace
   * @return string the full class name (with namespace) and with interface
   */
  private function getClassWithNamespace(string $class, string $namespace): string
  {
    if (strpos($class, 'Interface') === false) {
      $class .= 'Interface';
    }
    if (strpos($class, '\\') === false) {
      return $namespace . '\\' . $class;
    } else {
      return $class;
    }
  }
//</editor-fold desc="Private Methods">
}