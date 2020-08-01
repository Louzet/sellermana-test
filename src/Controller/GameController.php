<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameCreationType;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GameController extends AbstractController
{
    private EntityManagerInterface $em;

    private GameRepository $gameRepository;

    public function __construct(EntityManagerInterface $em, GameRepository $gameRepository)
    {
        $this->em = $em;
        $this->gameRepository = $gameRepository;
    }

    /**
     * @Route("/games", name="game.list", methods="GET")
     */
    public function gameList()
    {
        $games = $this->gameRepository->findAll();
        dump($games);

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
