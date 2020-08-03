<?php declare(strict_types=1);

namespace App\Event\State;

use App\Entity\State;
use Symfony\Contracts\EventDispatcher\Event;

class StateAddingPriorityEvent extends Event
{
    public const NAME = 'state.add_priority';

    private State $state;

    public function __construct(State $state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }
}