<?php

namespace App\Controller;

use App\Entity\Assigment;
use App\Form\Assigment1Type;
use App\Form\AssigmentType;
use App\Repository\AssigmentRepository;
use App\Repository\GalleryRepository;
use App\Util\GuidFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/assigment')]
final class AssigmentController extends AbstractController
{
    #[Route(name: 'app_assigment_index', methods: ['GET'])]
    public function index(AssigmentRepository $assigmentRepository): Response
    {

        return $this->render('assigment/index.html.twig', [
            'assigments' => $assigmentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_assigment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $assigment = new Assigment();
        $form = $this->createForm(AssigmentType::class, $assigment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $assigment->setUser($this->getUser());
            $assigment->setGuid(GuidFactory::generate());
            $entityManager->persist($assigment);
            $entityManager->flush();

            return $this->redirectToRoute('app_assigment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('assigment/new.html.twig', [
            'assigment' => $assigment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_assigment_show', methods: ['GET'])]
    public function show(Assigment $assigment, GalleryRepository $galleryRepository): Response
    {
        return $this->render('assigment/show.html.twig', [
            'assigment' => $assigment,
            'galleries' => $galleryRepository->findBy(['assigment' => $assigment, 'deleted' => false]),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_assigment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Assigment $assigment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AssigmentType::class, $assigment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_assigment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('assigment/edit.html.twig', [
            'assigment' => $assigment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_assigment_delete', methods: ['POST'])]
    public function delete(Request $request, Assigment $assigment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$assigment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($assigment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_assigment_index', [], Response::HTTP_SEE_OTHER);
    }
}
