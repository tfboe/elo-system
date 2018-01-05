<?php
declare(strict_types=1);


namespace App\Service;


use App\Service\RankingSystem\RankingSystemInterface;

/**
 * Interface DynamicServiceLoadingServiceInterface
 * @package App\Service
 */
interface DynamicServiceLoadingServiceInterface
{

//<editor-fold desc="Public Methods">
  /**
   * Loads the ranking service specified by name
   * @param string $name the name of the ranking service
   * @return RankingSystemInterface the service
   */
  public function loadRankingSystemService(string $name): RankingSystemInterface;
//</editor-fold desc="Public Methods">
}