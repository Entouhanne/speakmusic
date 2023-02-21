<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Musique;
use App\Entity\Auteur;
use App\Entity\SuivisMusique;
use App\Form\InterpretationType;
use App\Entity\Interpretation;
use App\Form\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Knp\Component\Pager\PaginatorInterface;

class ResultatsController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    #[Route('/resultats/{query}', name: 'app_resultats')]
    public function index(string $query, Request $request,  PaginatorInterface $paginator): Response
    {
        $task = new Musique();
        $form = $this->createForm(SearchType::class, $task);
        $donnees = $this->entityManager->getRepository(Musique::class)->findAllLike($query);
        $musique = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            4 // Nombre de résultats par page
        );
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

        return $this->render('resultats/index.html.twig', [
            'controller_name' => 'ResultatsController',
            'nom' => $query,
            'musique' => $musique,
            'form' => $form->createView()
        ]);
    }



    #[Route('/musique/{id}', name: 'app_musique')]
    public function musique(int $id, Request $request, ManagerRegistry $doctrine): Response
    {
        $musique = $this->entityManager->getRepository(Musique::class)->find($id);
        $auteur = $this->entityManager->getRepository(Auteur::class)->find($musique->getIdAuteur());
        $interpretations = $this->entityManager->getRepository(Interpretation::class)->findWithuser($musique->getId());
        return $this->render('resultats/musique.html.twig', [
            'musique' => $musique,
            'interpretation' => $interpretations,
            'auteur' => $auteur,
        ]);
    }

    #[Route('/auteur/{id}', name: 'app_auteur')]
    public function auteur(int $id, Request $request, ManagerRegistry $doctrine): Response
    {
        $auteur = $this->entityManager->getRepository(Auteur::class)->find($id);
        $musique = $this->entityManager->getRepository(Musique::class)->findBy(
            ['idAuteur' => $auteur->getId()],
        );
       // $interpretations = $this->entityManager->getRepository(Interpretation::class)->findWithuser($musique->getId());
        return $this->render('resultats/auteur.html.twig', [
            'auteur' => $auteur,
            'musique' => $musique,
        ]);
    }

    #[Route('/articles', name: 'app_article')]
    public function pagination(Request $request, PaginatorInterface $paginator) // Nous ajoutons les paramètres requis
    {
        $donnees = $this->entityManager->getRepository(Musique::class)->findAll();
        // Méthode findBy qui permet de récupérer les données avec des critères de filtre et de tri

        $articles = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            2 // Nombre de résultats par page
        );
        
        return $this->render('resultats/test.html.twig', [
            'articles' => $articles,
        ]);
    }
}
