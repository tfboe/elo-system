<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tests\Unit\App\Entity;

use App\Entity\Competition;
use App\Entity\Player;
use App\Entity\Team;
use Doctrine\Common\Collections\Collection;
use Tests\Helpers\UnitTestCase;

/**
 * Class TournamentTest
 * @package Tests\Unit\App\Entity
 */
class TeamTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Team::setCompetition
   * @covers \App\Entity\Team::getCompetition
   * @uses   \App\Entity\Competition
   * @uses   \App\Entity\Team::__construct
   * @uses   \App\Entity\Team::getStartNumber
   * @uses   \App\Entity\Team::setStartNumber
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
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
   * @covers \App\Entity\Team::__construct
   * @uses   \App\Entity\Helpers\NameEntity::getName
   * @uses   \App\Entity\Team::getPlayers
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
   * @covers \App\Entity\Team::getPlayers
   * @uses   \App\Entity\Team::__construct
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
   * @covers \App\Entity\Team::setRank
   * @covers \App\Entity\Team::getRank
   * @uses   \App\Entity\Team::__construct
   */
  public function testRank()
  {
    $team = $this->team();
    $team->setRank(1);
    self::assertEquals(1, $team->getRank());
  }

  /**
   * @covers \App\Entity\Team::setStartNumber
   * @covers \App\Entity\Team::getStartNumber
   * @uses   \App\Entity\Team::__construct
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