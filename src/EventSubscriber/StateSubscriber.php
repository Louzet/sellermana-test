<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\State\StateAddingPriorityEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StateSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            StateAddingPriorityEvent::NAME => 'addPriorityOnState'
        ];
    }

    public function addPriorityOnState(StateAddingPriorityEvent $event)
    {
        $state = $event->getState();

        $priority = $state::$statesOrder[$state->getCurrentState()];
        $reflection = new \ReflectionMethod($state, 'setPriority');
        $reflection->setAccessible(true);

        $reflection->invoke($state, $priority);
        $reflection->setAccessible(false);
    }
}