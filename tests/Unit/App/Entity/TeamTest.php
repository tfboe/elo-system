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
use App\Exceptions\ValueNotSet;
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
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
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
    /** @noinspection PhpUnhandledExceptionInspection */
    $team->setCompetition($competition);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($competition, $team->getCompetition());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $team->getCompetition()->getTeams()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($team, $team->getCompetition()->getTeams()[$team->getStartNumber()]);

    $competition2 = new Competition();

    /** @noinspection PhpUnhandledExceptionInspection */
    $team->setCompetition($competition2);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($competition2, $team->getCompetition());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $team->getCompetition()->getTeams()->count());
    self::assertEquals(0, $competition->getTeams()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($team, $team->getCompetition()->getTeams()[$team->getStartNumber()]);
  }

  /**
   * @covers \App\Entity\Team::getCompetition
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Team::__construct
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testCompetitionException()
  {
    $team = $this->team();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property competition of the class " . Team::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $team->getCompetition();
  }

  /**
   * @covers \App\Entity\Team::__construct
   * @uses   \App\Entity\Helpers\NameEntity::getName
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
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
    /** @noinspection PhpUnhandledExceptionInspection */
    $team->getPlayers()->set($player->getPlayerId(), $player);
    self::assertEquals(1, $team->getPlayers()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($player, $team->getPlayers()[$player->getPlayerId()]);
  }

  /**
   * @covers \App\Entity\Team::setRank
   * @covers \App\Entity\Team::getRank
   * @uses   \App\Entity\Team::__construct
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   */
  public function testRank()
  {
    $team = $this->team();
    $team->setRank(1);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $team->getRank());
  }

  /**
   * @covers \App\Entity\Team::getRank
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Team::__construct
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testRankException()
  {
    $team = $this->team();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property rank of the class " . Team::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $team->getRank();
  }

  /**
   * @covers \App\Entity\Team::setStartNumber
   * @covers \App\Entity\Team::getStartNumber
   * @uses   \App\Entity\Team::__construct
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   */
  public function testStartNumber()
  {
    $team = $this->team();
    $team->setStartNumber(1);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $team->getStartNumber());
  }

  /**
   * @covers \App\Entity\Team::getStartNumber
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Team::__construct
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testStartNumberException()
  {
    $team = $this->team();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property startNumber of the class " . Team::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $team->getStartNumber();
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