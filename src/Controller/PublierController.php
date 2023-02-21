<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Musique;
use App\Entity\Auteur;
use App\Form\InterpretationType;
use App\Entity\Interpretation;
use App\Form\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Datetime;

class PublierController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    #[Route('/publier/{id}', name: 'app_publier')]
    public function index(int $id, Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $musique = $this->entityManager->getRepository(Musique::class)->find($id);
    $auteur = $this->entityManager->getRepository(Auteur::class)->find($musique->getIdAuteur());
    $task = new Interpretation();
    $task->setIdMusique($musique);
    $form = $this->createForm(InterpretationType::class, $task);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

            $task = $form->getData();
            $entityManager = $doctrine->getManager();
            $product = new Interpretation();
            $product->setTitre($task->getTitre());
            $product->setDescription($task->getDescription());
            $product->setIdMusique($musique);
            $product->setDate(new \DateTime('now'));
            $product->setIdUser($this->getUser());
            $entityManager->persist($product);
            $entityManager->flush();

            //return $this->redirectToRoute('app_resultats', ['query' => $task->getNom()]);
            return $this->redirectToRoute(
                'app_accueil'
            );
    }

        return $this->render('publier/index.html.twig', [
            'controller_name' => 'PublierController',
            'form' => $form->createView(),
        'musique' => $musique,
        'auteur' => $auteur
        ]);
    }
}