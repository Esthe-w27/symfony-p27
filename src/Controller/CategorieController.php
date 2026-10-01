<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Form\ArticleType;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findAll();
        return $this->render('categorie/index.html.twig', [
            'categories' => $categories
        ]);
    }
    #[Route('/deleteCategorie/{id}', name: 'app_categorie_delete')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(Categorie $categorie,  EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($categorie);
        $entityManager->flush();
        return $this->redirectToRoute('app_categorie');
    }

    #[Route('/editCategorie/{id}', name: 'app_categorie_edit')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Categorie $categorie, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);

        // Vérifie si le formulaire est envoyé ou non
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // récupère les données du formulaire
            $categorie = $form->getData();

            $entityManager->persist($categorie); // on ajoute l'article dans l'entity manager pour qu'il puisse s'en occuper au moment du flush
            $entityManager->flush(); // on execute les req en BDD
            return $this->redirectToRoute('app_categorie');
        }

        return $this->render('categorie/add.html.twig', [
            'titre' => "Modifier la catégorie",
            'form' => $form,
        ]);
    }

    #[Route('/addCategorie', name: 'app_categorie_add')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class, $categorie);

        // Vérifie si le formulaire est envoyé ou non
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // récupère les données du formulaire
            $categorie = $form->getData();

            $entityManager->persist($categorie); // on ajoute l'article dans l'entity manager pour qu'il puisse s'en occuper au moment du flush
            $entityManager->flush(); // on execute les req en BDD
            return $this->redirectToRoute('app_categorie');
        }

        return $this->render('categorie/add.html.twig', [
            'titre' => "Ajouter une catégorie",
            'form' => $form,
        ]);
    }
}
