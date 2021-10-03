<?php
declare(strict_types=1);

namespace App\Policies;

use App\Entity\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class PlayerPolicy
 * @package App\Policies
 */
class PlayerPolicy
{
  use HandlesAuthorization;

//<editor-fold desc="Public Methods">

  /**
   * Determine whether the user can create new players.
   *
   * @param  User $user
   * @return mixed
   */
  public function create(User $user): bool
  {
    return $user->canCreateTournaments();
  }
//</editor-fold desc="Public Methods">
}
