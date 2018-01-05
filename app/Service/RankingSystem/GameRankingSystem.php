<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 2:34 PM
 */

namespace App\Service\RankingSystem;


use App\Helpers\Level;


/**
 * Class GameRankingSystem
 * @package App\Service\RankingSystem
 */
abstract class GameRankingSystem extends RankingSystem implements GameRankingSystemInterface
{
//<editor-fold desc="Protected Methods">
  /**
   * @inheritDoc
   */
  protected function getLevel(): int
  {
    return Level::GAME;
  }
//</editor-fold desc="Protected Methods">

}