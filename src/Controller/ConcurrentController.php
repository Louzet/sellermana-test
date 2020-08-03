<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Concurrent;
use App\Repository\ConcurrentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Concurrent\CreateConcurrentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConcurrentController extends AbstractController
{
    private $concurrentRepository;
    private $em;

    public function __construct(ConcurrentRepository $concurrentRepository, EntityManagerInterface $em)
    {
        $this->concurrentRepository = $concurrentRepository;
        $this->em = $em;
    }

    /**
     * @Route("/concurrents", name="concurrent.list", methods="GET")
     */
    public function concurrentsList()
    {
        $concurrents = $this->concurrentRepository->findAll();

        return $this->render('concurrent/list.html.twig', [
            'concurrents' => $concurrents
        ]);
    }

    /**
     * @Route("/concurrent", name="concurrent.create", methods={"GET", "POST"})
     */
    public function createConcurrent(Request $request)
    {
        $concurent = new Concurrent();

        $form = $this->createForm(CreateConcurrentType::class, $concurent)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($concurent);
            $this->em->flush();

            $this->addFlash('success', 'concurrent.flash.created');

            return $this->redirectToRoute('concurrent.list');
        }

        return $this->render('concurrent/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}