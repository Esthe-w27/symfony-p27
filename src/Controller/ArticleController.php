<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll();
        return $this->render('article/index.html.twig', [
            'controller_name' => ($this->getUser() ) ?  $this->getUser()->getNom() : "??",
            'articles' => $articles,
        ]);
    }

    #[Route('/article_show/{id}', name: 'article_show')]
    public function show(Article $article): Response
    {

        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/articleRandom', name: 'app_article_random')]
    public function random(): Response
    {
        $number = random_int(0, 100);

        return $this->render('article/random.html.twig', [
            'number' => $number,
        ]);
    }


    #[Route('/addArticle', name: 'app_article_add')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        // Vérifie si le formulaire est envoyé ou non
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // récupère les données du formulaire
            $article = $form->getData();
            $article->setAuteur($this->getUser());
            $entityManager->persist($article); // on ajoute l'article dans l'entity manager pour qu'il puisse s'en occuper au moment du flush
            $entityManager->flush(); // on execute les req en BDD
            return $this->redirectToRoute('app_article');
        }

        return $this->render('article/add.html.twig', [
            'form' => $form,
        ]);
    }
}
