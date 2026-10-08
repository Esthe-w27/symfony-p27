<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EvenementController extends AbstractController
{
    #[Route('/evenements', name: 'evenement_index')]
    public function index(EvenementRepository $repo): Response
    {
        return $this->render('evenement/index.html.twig', [
            'evenements' => $repo->findBy([], ['date' => 'ASC']),
        ]);
    }

    #[Route('/evenements/{id}', name: 'evenement_show', requirements: ['id' => '\d+'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', ['evenement' => $evenement]);
    }

    #[Route('/admin/evenements/new', name: 'evenement_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenement->setCreateur($this->getUser());
            $em->persist($evenement);
            $em->flush();
            return $this->redirectToRoute('evenement_index');
        }
        return $this->render('form.html.twig', ['form' => $form, 'titre' => 'Nouvel événement']);
    }

    #[Route('/admin/evenements/{id}/edit', name: 'evenement_edit')]
    public function edit(Evenement $evenement, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('evenement_index');
        }
        return $this->render('form.html.twig', ['form' => $form, 'titre' => "Modifier l'événement"]);
    }

    #[Route('/admin/evenements/{id}/delete', name: 'evenement_delete')]
    public function delete(Evenement $evenement, EntityManagerInterface $em): Response
    {
        $em->remove($evenement);
        $em->flush();
        return $this->redirectToRoute('evenement_index');
    }
}