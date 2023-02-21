<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Musique;
use App\Form\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class AccueilController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_accueil')]
    public function index(Request $request): Response
    {
        $task = new Musique();
        $form = $this->createForm(SearchType::class, $task);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $task = $form->getData();
                //return $this->redirectToRoute('app_resultats', ['query' => $task->getNom()]);
                return $this->redirectToRoute(
                    'app_resultats',
                    array('query' => $task->getNom()),
                    Response::HTTP_MOVED_PERMANENTLY // = 301
                );
            }

        return $this->render('accueil/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
