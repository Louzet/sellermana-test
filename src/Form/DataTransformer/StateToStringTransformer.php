<?php declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Entity\State;
use Symfony\Component\Form\DataTransformerInterface;

class StateToStringTransformer implements DataTransformerInterface
{
    public function transform($state)
    {
        if (null === $state) {
            return State::STATE_GOOD;
        }

        return $state->getCurrentState();
    }

    public function reverseTransform($stateString)
    {
        $state = new State();
        $state->setCurrentState($stateString);

        return $state;
    }
}