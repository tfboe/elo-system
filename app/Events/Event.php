<?php
declare(strict_types=1);

namespace App\Events;

use Illuminate\Queue\SerializesModels;

/**
 * Class Event
 * @package App\Events
 */
abstract class Event
{
  use SerializesModels;
}
