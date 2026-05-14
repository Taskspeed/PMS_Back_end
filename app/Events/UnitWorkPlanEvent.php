<?php

namespace App\Events;


use Illuminate\Broadcasting\InteractsWithSockets;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UnitWorkPlanEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public $unitworkplan;
      public $user;

    public function __construct($unitworkplan,$user)
    {

        $this->unitworkplan = $unitworkplan;
              $this->user = $user;

    }


}
