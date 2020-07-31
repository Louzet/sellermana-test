<?php declare(strict_types=1);

namespace App\Entity;

class State
{

    protected const STATE_MEDIUM    = "Etat moyen";
    protected const STATE_NORMAL    = "Bon état";
    protected const STATE_GOOD      = "Très bon état";
    protected const STATE_VERY_GOOD = "Comme neuf";
    protected const STATE_PERFECT   = "Neuf";

    private string $currentState = self::STATE_GOOD;

    public function getCurrentState()
    {
        return $this->currentState;
    }

    public function setCurrentState(string $state)
    {
        $this->currentState = $state;

        return $this;
    }
}
