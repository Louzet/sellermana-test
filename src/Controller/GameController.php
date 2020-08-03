<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\Game\GameCreationType;
use App\Repository\GameRepository;
use App\Event\Game\GameCreationEvent;
use Doctrine\ORM\EntityManagerInterface;
use App\Event\State\StateAddingPriorityEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GameController extends AbstractController
{
    private EntityManagerInterface $em;

    private GameRepository $gameRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, GameRepository $gameRepository, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->gameRepository = $gameRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @Route("/games", name="game.list", methods="GET")
     */
    public function gameList()
    {
        $games = $this->gameRepository->findAll();

        return $this->render('game/list.html.twig', [
            'games' => $games,
        ]);
    }

    /**
     * @Route("/game", name="game.create", methods={"GET", "POST"})
     */
    public function gameCreate(Request $request)
    {
        $game = new Game();

        $form = $this->createForm(GameCreationType::class, $game)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new GameCreationEvent($game);
            $stateEvent = new StateAddingPriorityEvent($game->getState());

            $this->dispatcher->dispatch($stateEvent, $stateEvent::NAME);
            $this->dispatcher->dispatch($event, $event::NAME);

            $this->em->persist($game);
            $this->em->flush();

            $this->addFlash('success', 'game.flash.created');

            return $this->redirectToRoute('game.list');
        }

        return $this->render('game/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
