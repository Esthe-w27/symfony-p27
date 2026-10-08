<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\EvenementRepository;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LieuController extends AbstractController
{
    #[Route('/lieux', name: 'lieu_index')]
    public function index(LieuRepository $repo): Response
    {
        return $this->render('lieu/index.html.twig', ['lieux' => $repo->findAll()]);
    }

    #[Route('/lieux/{id}/evenements', name: 'lieu_evenements', requirements: ['id' => '\d+'])]
    public function evenements(Lieu $lieu, EvenementRepository $repo): Response
    {
        return $this->render('evenement/index.html.twig', [
            'evenements' => $repo->findBy(['lieu' => $lieu], ['date' => 'ASC']),
        ]);
    }

    #[Route('/admin/lieux/new', name: 'lieu_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($lieu);
            $em->flush();
            return $this->redirectToRoute('lieu_index');
        }
        return $this->render('form.html.twig', ['form' => $form, 'titre' => 'Nouveau lieu']);
    }

    #[Route('/admin/lieux/{id}/edit', name: 'lieu_edit')]
    public function edit(Lieu $lieu, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('lieu_index');
        }
        return $this->render('form.html.twig', ['form' => $form, 'titre' => 'Modifier le lieu']);
    }

    #[Route('/admin/lieux/{id}/delete', name: 'lieu_delete')]
    public function delete(Lieu $lieu, EntityManagerInterface $em): Response
    {
        $em->remove($lieu);
        $em->flush();
        return $this->redirectToRoute('lieu_index');
    }
}