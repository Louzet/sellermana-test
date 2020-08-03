<?php

namespace App\EventSubscriber;

use App\Entity\Game;
use App\Repository\GameRepository;
use App\Event\Game\GameCreationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GameSubscriber implements EventSubscriberInterface
{
    private GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            GameCreationEvent::NAME => 'onGameCreation',
        ];
    }

    public function onGameCreation(GameCreationEvent $event)
    {
        $game = $event->getGame();
        // find all games with the same name and same state
        $games = $this->gameRepository
            ->findGameByNameAndState($game->getName(), $game->getState());


        // find the cheapest game
        $cheapest = $this->findCheapestGame($games);

        if (null !== $cheapest) {
            $game->setPrice($cheapest->getPrice() - $game::CENTIME);
        } else {
            $averagePrice = ($game->getFloorPrice() + $game->getMaxPrice()) / 2;
            $game->setPrice($averagePrice);
        }
    }

    /**
     * @param Game[] $games
     *
     * @return null|Game
     */
    private function findCheapestGame(array $games): ?Game
    {
        if(\count($games) === 0) {
            return null;
        }

        if(\count($games) === 1) {
            return $games[0];
        }

        $cheapest = null;
        $lowerPrice = 0;

        foreach($games as $game) {
            if($lowerPrice === 0) {
                $lowerPrice = $game->getPrice();
                $cheapest = $game;
            }
            if ($game->getPrice() < $lowerPrice) {
                $lowerPrice = $game->getPrice();
                $cheapest = $game;
            }
        }

        return $cheapest;;
    }
}
