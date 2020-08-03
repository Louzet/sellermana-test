<?php declare(strict_types=1);

namespace App\Event\Game;

use App\Entity\Game;
use Symfony\Contracts\EventDispatcher\Event;

class GameCreationEvent extends Event
{
    public const NAME = 'game.creation';

    protected Game $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function getGame(): Game
    {
        return $this->game;
    }
}