<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/5/18
 * Time: 10:54 PM
 */

namespace App\Entity;


use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\RankingSystemChangeInterface;

/**
 * Class RankingSystemList
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="rankingSystemChanges")
 *
 * Dynamic method hints for Elo ranking
 * @method int getPlayedGames()
 * @method setPlayedGames(int $playedGames)
 * @method int getRatedGames()
 * @method setRatedGames(int $ratedGames)
 * @method float getProvisoryRanking()
 * @method setProvisoryRanking(float $provisoryRanking)
 */
class RankingSystemChange extends BaseEntity implements RankingSystemChangeInterface
{
  use \Tfboe\FmLib\Entity\Traits\RankingSystemChange;

//<editor-fold desc="Constructor">

  /**
   * RankingSystem constructor.
   * @param array $keys
   */
  public function __construct(array $keys)
  {
    $this->init($keys);
  }
//</editor-fold desc="Constructor">
}