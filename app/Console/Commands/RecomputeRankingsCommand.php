<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/28/18
 * Time: 1:16 PM
 */

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Tfboe\FmLib\Service\RankingSystemServiceInterface;

class RecomputeRankingsCommand extends Command
{
//<editor-fold desc="Fields">
  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'recompute-rankings';
  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = "Recomputes all needed ranking systems";
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * Execute the console command.
   *
   * @return void
   */
  public function handle(RankingSystemServiceInterface $rss)
  {
    $rss->recalculateRankingSystems();
  }
//</editor-fold desc="Public Methods">
}