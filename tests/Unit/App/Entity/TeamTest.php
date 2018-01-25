<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tests\Tfboe\FmLib\Entity;

use Tfboe\FmLib\Entity\Competition;
use Tfboe\FmLib\Entity\Player;
use Tfboe\FmLib\Entity\Team;
use Doctrine\Common\Collections\Collection;
use Tests\Helpers\UnitTestCase;

/**
 * Class TournamentTest
 * @package Tests\Tfboe\FmLib\Entity
 */
class TeamTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Team::setCompetition
   * @covers \Tfboe\FmLib\Entity\Team::getCompetition
   * @uses   \Tfboe\FmLib\Entity\Competition
   * @uses   \Tfboe\FmLib\Entity\Team::__construct
   * @uses   \Tfboe\FmLib\Entity\Team::getStartNumber
   * @uses   \Tfboe\FmLib\Entity\Team::setStartNumber
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testCompetition()
  {
    $team = $this->team();
    $competition = new Competition();
    $team->setStartNumber(1);
    $team->setCompetition($competition);
    self::assertEquals($competition, $team->getCompetition());
    self::assertEquals(1, $team->getCompetition()->getTeams()->count());
    self::assertEquals($team, $team->getCompetition()->getTeams()[$team->getStartNumber()]);

    $competition2 = new Competition();

    $team->setCompetition($competition2);
    self::assertEquals($competition2, $team->getCompetition());
    self::assertEquals(1, $team->getCompetition()->getTeams()->count());
    self::assertEquals(0, $competition->getTeams()->count());
    self::assertEquals($team, $team->getCompetition()->getTeams()[$team->getStartNumber()]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Team::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::getName
   * @uses   \Tfboe\FmLib\Entity\Team::getPlayers
   */
  public function testConstructor()
  {
    $team = $this->team();
    self::assertInstanceOf(Team::class, $team);
    self::assertInstanceOf(Collection::class, $team->getPlayers());
    self::assertEquals(0, $team->getPlayers()->count());
    self::assertEquals("", $team->getName());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Team::getPlayers
   * @uses   \Tfboe\FmLib\Entity\Team::__construct
   */
  public function testPlayers()
  {
    $team = $this->team();
    /** @var Player $player */
    $player = $this->createStubWithId(Player::class, 1, 'getPlayerId');
    $team->getPlayers()->set($player->getPlayerId(), $player);
    self::assertEquals(1, $team->getPlayers()->count());
    self::assertEquals($player, $team->getPlayers()[$player->getPlayerId()]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Team::setRank
   * @covers \Tfboe\FmLib\Entity\Team::getRank
   * @uses   \Tfboe\FmLib\Entity\Team::__construct
   */
  public function testRank()
  {
    $team = $this->team();
    $team->setRank(1);
    self::assertEquals(1, $team->getRank());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Team::setStartNumber
   * @covers \Tfboe\FmLib\Entity\Team::getStartNumber
   * @uses   \Tfboe\FmLib\Entity\Team::__construct
   */
  public function testStartNumber()
  {
    $team = $this->team();
    $team->setStartNumber(1);
    self::assertEquals(1, $team->getStartNumber());
  }

//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return Team a new team
   */
  private function team(): Team
  {
    return new Team();
  }
//</editor-fold desc="Private Methods">
}