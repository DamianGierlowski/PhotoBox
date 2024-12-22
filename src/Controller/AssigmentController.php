<?php

namespace App\Controller;

use App\Entity\Assigment;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Form\AssigmentType;
use App\Repository\AssigmentRepository;
use App\Repository\GalleryRepository;
use App\UniqueNameInterface\PermissionInterface;
use App\Util\GuidFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/assigment')]
final class AssigmentController extends AbstractController
{
    #[Route(name: 'app_assigment_index', methods: ['GET'])]
    public function index(AssigmentRepository $assigmentRepository): Response
    {

        return $this->render('assigment/index.html.twig', [
            'assigments' => $assigmentRepository->findAllForUser($this->getUser()),
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

    #[Route('/{guid}', name: 'app_assigment_show', methods: ['GET'])]
    public function show(
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Assigment $assigment,
        GalleryRepository $galleryRepository): Response
    {
        $this->isGranted(PermissionInterface::OWNER, $assigment);

        return $this->render('assigment/show.html.twig', [
            'assigment' => $assigment,
            'galleries' => $galleryRepository->findBy(['assigment' => $assigment, 'deleted' => false]),
        ]);
    }

    #[Route('/{guid}/edit', name: 'app_assigment_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Assigment $assigment,
        EntityManagerInterface $entityManager): Response
    {
        $this->isGranted(PermissionInterface::OWNER, $assigment);

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

    #[Route('/{guid}', name: 'app_assigment_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Assigment $assigment,
        EntityManagerInterface $entityManager): Response
    {
        $this->isGranted(PermissionInterface::OWNER, $assigment);

        if ($this->isCsrfTokenValid('delete'.$assigment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($assigment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_assigment_index', [], Response::HTTP_SEE_OTHER);
    }
}
