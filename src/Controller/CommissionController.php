<?php

namespace App\Controller;

use App\Entity\Commission;

use App\Form\Commission\CommissionSearchType;
use App\Form\CommissionType;
use App\Repository\CommissionRepository;
use App\Repository\GalleryRepository;
use App\Service\Commission\CommissionRenderService;
use App\UniqueNameInterface\PermissionInterface;
use App\Util\GuidFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
#[Route('/commission')]
final class CommissionController extends AbstractController
{

    #[Route(name: 'app_commission_index', methods: ['GET'])]
    public function index(CommissionRenderService $commissionRenderService): Response
    {
        $form = $this->createForm(CommissionSearchType::class);

        return $this->render('commission/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_commission_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CommissionRenderService $commissionRenderService): Response
    {
        $contentHeaderData = $commissionRenderService->getHeaderRenderDataForForms();

        $commission = new Commission();
        $form = $this->createForm(CommissionType::class, $commission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commission->setUser($this->getUser());
            $commission->setGuid(GuidFactory::generate());
            $entityManager->persist($commission);
            $entityManager->flush();

            return $this->redirectToRoute('app_commission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commission/new.html.twig', [
            'content_header' => $contentHeaderData,
            'form' => $form,
        ]);
    }

    #[Route('/{guid}', name: 'app_commission_show', methods: ['GET'])]
    public function show(
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Commission        $commission,
        CommissionRenderService $commissionRenderService
    ): Response
    {
        $this->isGranted(PermissionInterface::OWNER, $commission);

        $contentHeaderData = $commissionRenderService->getHeaderRenderDataForShow($commission);
        $contentHeaderGalleryData = $commissionRenderService->getHeaderRenderDataForGalleryShow($commission);
        $galleryTable = $commissionRenderService->getTableRenderDataForGalleries($commission);

        return $this->render('commission/show.html.twig', [
            'content_header' => $contentHeaderData,
            'commission_title' => $commission->getTitle(),
            'gallery_content_header' => $contentHeaderGalleryData,
            'table_data' => $galleryTable,
            'entity_data' => [
                'commission.fields.date' => $commission->getDate()->format('d.m.Y H:i:s'),
                'commission.fields.description' => $commission->getDescription(),
            ],
        ]);
    }

    #[Route('/{guid}/edit', name: 'app_commission_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Commission             $assigment,
        EntityManagerInterface $entityManager,
        CommissionRenderService $commissionRenderService,
    ): Response {
        $this->isGranted(PermissionInterface::OWNER, $assigment);

        $contentHeaderData = $commissionRenderService->getHeaderRenderDataForForms();

        $form = $this->createForm(CommissionType::class, $assigment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commission/edit.html.twig', [
            'commission' => $assigment,
            'content_header' => $contentHeaderData,
            'form' => $form,
        ]);
    }

    #[Route('/{guid}', name: 'app_assigment_delete', methods: ['POST'])]
    public function delete(
        Request                $request,
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Commission             $assigment,
        EntityManagerInterface $entityManager): Response
    {
        $this->isGranted(PermissionInterface::OWNER, $assigment);

        if ($this->isCsrfTokenValid('delete'.$assigment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($assigment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commission_index', [], Response::HTTP_SEE_OTHER);
    }
}
