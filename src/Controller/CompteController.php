<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Musique;
use App\Entity\User;
use App\Entity\Interpretation;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\SuivisMusique;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\ChangerPhotoProfilType;
use App\Form\ChangePasswordType;
use App\Form\ChangerPseudoType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class CompteController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte', name: 'app_compte')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $doctrine->getManager();
        $user = $this->getUser();
        $form_pseudo = $this->createForm(ChangerPseudoType::class, $user);
        $form_pseudo->handleRequest($request);
        if ($form_pseudo->isSubmitted() && $form_pseudo->isValid()) {
            $changer_pseudo = $form_pseudo->get("pseudo")->getData();
            $user->setPseudo($changer_pseudo);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_compte');
        }
        $product = $doctrine->getRepository(SuivisMusique::class)->findWithMusique($user = $this->getUser()->getId());
        return $this->render('compte/index.html.twig', [
            'controller_name' => 'CompteController',
            'test' => $product,
            'user' => $user = $this->getUser(),
            'form_pseudo' => $form_pseudo->createView()
        ]);
    }

    #[Route('/suivre/{id}', name: 'app_suivre')]
    public function suivre(int $id, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $query = $doctrine->getRepository(Musique::class)->find($id);
        $entityManager = $doctrine->getManager();

        $product = new SuivisMusique();
        $product->setIdUser($this->getUser());
        $product->setIdMusique($query);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        return $this->redirectToRoute(
            'app_compte'
        );
    }

    #[Route('/changerphoto', name: 'app_changerphoto')]
    public function changerphoto(ManagerRegistry $doctrine,  SluggerInterface $slugger, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $doctrine->getManager();
        $musique = new User();
        $form = $this->createForm(ChangerPhotoProfilType::class, $musique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('Illustration')->getData();

            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('image_profil_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user = $this->getUser();
                $user->setImageProfil($newFilename);
                $entityManager->persist($user);
                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();
                return $this->redirectToRoute('app_accueil');
            }

            // ... persist the $product variable or any other work

            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('compte/photo_profil.html.twig', [
            'form' => $form->createView() 
        ]);
    }

    #[Route('/supprimer_suivis/{id}', name: 'app_supprimer_suivis')]
    public function supprimer_suivis(int $id, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $query = $doctrine->getRepository(SuivisMusique::class)->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($query);
        $entityManager->flush();
        return $this->redirectToRoute(
            'app_compte'
        );
    }

    #[Route('/changer_mot_passe', name: 'app_changer_mdp')]
    public function changer_MDP(ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->redirectToRoute(
            'app_compte'
        );
    }

    #[Route('/compte/editer_mot_passe', name: 'app_editer_mot_passe')]
    public function editer_mot_passe(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $notification = null;
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user); 
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $old_pwd = $form->get('old_password')->getData();
        if ($encoder->isPasswordValid($user, $old_pwd)) {
            $new_pwd = $form->get('new_password')->getData();
            $hashedPassword = $encoder->hashPassword($user, $new_pwd);
            $user->setPassword($hashedPassword);
            $this->entityManager->flush();
            $notification = "Votre mot de passe a bien été mis à jour";
            return $this->redirectToRoute(
                'succes_password'
            );
        } else {
            $notification = "L'ancien mot de passe n'est pas valide";
        }
        }
        return $this->render('compte/mot_passe.html.twig', [
            'form' => $form->createView(), 
            'notification' => $notification
        ]);
    }

    #[Route('/succes_password', name: 'succes_password')]
    public function succes_password(Request $request): Response
    {
        return $this->render('compte/succes.html.twig', [
        ]);
    }

    #[Route('/utilisateur/{id}', name: 'app_utilisateur')]
    public function utilisateur(int $id, ManagerRegistry $doctrine): Response
    {
        $query = $doctrine->getRepository(User::class)->find($id);
        $interpretations = $doctrine->getRepository(Interpretation::class)->findBy(
            ['idUser' => $query]
        );
        $entityManager = $doctrine->getManager();
        return $this->render('compte/utilisateur.html.twig', [
            'utilisateur' => $query,
            'interpretations' => $interpretations,
        ]);
    }
}


