<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Form\ChosirTypeAjoutType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AjoutMusiqueType;
use App\Form\AjoutAuteurType;
use App\Form\AjoutGenreType;
use App\Form\AjoutAlbumType;
use App\Entity\Musique;
use App\Entity\Genre;
use App\Entity\TypeAuteur;
use App\Entity\Pays;
use App\Entity\Album;
use App\Entity\Auteur;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\ChercherAlbumType;
use App\Form\ChercherPaysType;
use App\Form\ChercherGenreType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;



class AjoutController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    #[Route('/ajout', name: 'app_ajout')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(ChosirTypeAjoutType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
           
            if ($task["type"] == "genre") {
                return $this->redirectToRoute('app_ajout_genre');
            } else if ($task["type"] == "auteur") {
                return $this->redirectToRoute('app_choisir_genre');
            } else if ($task["type"] == "album") {
                return $this->redirectToRoute('app_ajout_album');
            } else if ($task["type"] == "musique") {
                return $this->redirectToRoute('app_choisir_album');
            }
        }
        return $this->render('ajout/index.html.twig', [
            'controller_name' => 'AjoutController',
            'form' => $form->createView(),
        ]);
    }



    #[Route('/choisir-album/{slug}', name: 'app_choisir_album')]
    public function choisir_auteur(Request $request, string $slug = "inside"): Response
    {
        $task = new Album();
        $form = $this->createForm(ChercherAlbumType::class, $task);
        $donnees = $this->entityManager->getRepository(Album::class)->findAllLike($slug);
        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $task = $form->getData();
                return $this->redirectToRoute(
                    'app_choisir_album',
                    array('slug' => $task->getNom()),
                    Response::HTTP_MOVED_PERMANENTLY
                );
            }
            return $this->render('ajout/choisir_album.html.twig', [
                'form' => $form->createView(),
                'slug' => $slug,
                'test' => $donnees
            ]);
        }

    #[Route('/choisir-pays/{idGenre}/{slug}', name: 'app_choisir_pays')]
    public function choisir_pays(Request $request, int $idGenre, string $slug = "fr"): Response
    {
        $task = new Pays();
        $form = $this->createForm(ChercherPaysType::class, $task);
        $donnees = $this->entityManager->getRepository(Pays::class)->findAllLike($slug);
        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $task = $form->getData();
                return $this->redirectToRoute(
                    'app_choisir_pays',
                    array('slug' => $task->getNom(),
                    'idGenre' => $idGenre),
                    Response::HTTP_MOVED_PERMANENTLY
                );
            }
            return $this->render('ajout/pays.html.twig', [
                'form' => $form->createView(),
                'slug' => $slug,
                'test' => $donnees,
                'genre' => $idGenre
            ]);
        }

    
        #[Route('/choisir-genre/{slug}', name: 'app_choisir_genre')]
        public function choisir_genre(Request $request, string $slug = "pop"): Response
        {
            $task = new Genre();
            $form = $this->createForm(ChercherGenreType::class, $task);
            $donnees = $this->entityManager->getRepository(Genre::class)->findAllLike($slug);
            $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $task = $form->getData();
                    return $this->redirectToRoute(
                        'app_choisir_genre',
                        array('slug' => $task->getNom()),
                        Response::HTTP_MOVED_PERMANENTLY
                    );
                }
                return $this->render('ajout/genre.html.twig', [
                    'form' => $form->createView(),
                    'slug' => $slug,
                    'test' => $donnees
                ]);
            }


    #[Route('/ajout/musique/{id_album}', name: 'app_ajout_musique')]
    public function ajout_musique(int $id_album, Request $request, SluggerInterface $slugger, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $doctrine->getManager();
        $album = $doctrine->getRepository(Album::class)->find($id_album);
        $auteur = $doctrine->getRepository(Auteur::class)->find($album->getIdAuteur());
        $product = new Musique();
        $form = $this->createForm(AjoutMusiqueType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $product->setNom($form->get('nom')->getData());
                $product->setParoles('test');
                $product->setIdAuteur($auteur);
                $product->setIdAlbum($album);
                $entityManager->persist($product);
                $entityManager->flush();

                return $this->redirectToRoute('app_accueil');
            }

        return $this->render('ajout/musique.html.twig', [
            'form' => $form->createView(),
            'auteur' => $auteur
        ]);
    }



    #[Route('/ajout/auteur/{genre}/{pays}', name: 'app_ajout_auteur')]
    public function ajout_auteur(Request $request, SluggerInterface $slugger, ManagerRegistry $doctrine, int $genre = 3, int $pays =3): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $doctrine->getManager();
        $product = new Auteur();
        $form = $this->createForm(AjoutAuteurType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('Illustration')->getData();

            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('illustrations_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $product->setNom($form->get('nom')->getData());
                $product->setDateFormation($form->get('dateFormation')->getData());
                $product->setIllustration($newFilename);
                $product->setIdGenre($doctrine->getRepository(Genre::class)->find($genre));
                $product->setPays($doctrine->getRepository(Pays::class)->find($pays));
                $product->setType($doctrine->getRepository(TypeAuteur::class)->find(1));
                $entityManager->persist($product);

                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();
            }

            // ... persist the $product variable or any other work

            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('ajout/ajout_auteur.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/ajout/genre', name: 'app_ajout_genre')]
    public function ajout_genre(Request $request, SluggerInterface $slugger, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $doctrine->getManager();
        $genre = new Genre();
        $form = $this->createForm(AjoutGenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $genre->setNom($form->get('nom')->getData());
                $entityManager->persist($genre);
                $entityManager->flush();
                return $this->redirectToRoute('succes_ajout');
            }


        return $this->render('ajout/ajout_genre.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/succes_ajout', name: 'succes_ajout')]
    public function succes_ajout(Request $request): Response
    {
        return $this->render('ajout/succes.html.twig', [
        ]);
    }



    #[Route('/ajout/album', name: 'app_ajout_album')]
    public function ajout_album(Request $request, SluggerInterface $slugger, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $doctrine->getManager();
        $product = new Album();
        $form = $this->createForm(AjoutAlbumType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('Illustration')->getData();

            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('illustrations_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }
                $product->setNom($form->get('nom')->getData());
                $product->setIllustration($newFilename);
                $entityManager->persist($product);
                $entityManager->flush();
            }
            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('ajout/album.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}