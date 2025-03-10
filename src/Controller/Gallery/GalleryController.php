<?php

namespace App\Controller\Gallery;

use App\Entity\Gallery;
use App\Form\Gallery\UploadGalleryFilesType;
use App\Form\GalleryType;
use App\Repository\CommissionRepository;
use App\Service\Gallery\GalleryRenderService;
use App\Service\Gallery\GalleryService;
use App\Service\Gallery\UploadService;
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


#[IsGranted('ROLE_USER')]
#[Route('/gallery')]
final class GalleryController extends AbstractController
{

    #[Route('/', name: 'app_gallery_index', methods: ['GET'])]
    public function index(GalleryRenderService $galleryRenderService)
    {
        $user = $this->getUser();

        return $this->render('gallery/index.html.twig', [
            'content_header' => $galleryRenderService->getHeaderRenderDataForIndex(),
            'table_data' => $galleryRenderService->getTableRenderDataForIndex($user),
        ]);
    }


    #[Route('/new', name: 'app_gallery_new', methods: ['GET', 'POST'])]
    public function new(
        Request                $request,
        EntityManagerInterface $entityManager,
        GalleryRenderService   $galleryRenderService,
        CommissionRepository $commissionRepository,
    ): Response {

        $gallery = new Gallery();
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        $commissionGuid = $request->get('guid') ?? null;

        $contentHeaderData = $galleryRenderService->getHeaderRenderDataForNew($commissionGuid);

        if ($form->isSubmitted() && $form->isValid()) {

            $commission = $commissionRepository->findOneBy(['guid' => $commissionGuid]);
            $gallery->setGuid(GuidFactory::generate());
            $gallery->setCreatedBy($this->getUser());
            $gallery->setCommission($commission);

            $entityManager->persist($gallery);
            $entityManager->flush();

            if (null === $commissionGuid) {
                return $this->redirectToRoute('app_gallery_index');
            }

            return $this->redirectToRoute('app_commission_show', ['guid' => $commissionGuid], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gallery/new.html.twig', [
            'content_header' => $contentHeaderData,
            'gallery' => $gallery,
            'form' => $form,
        ]);
    }

    #[Route('/{guid}', name: 'app_gallery_show', methods: ['GET'])]
    public function show(
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Gallery              $gallery,
        GalleryRenderService $galleryRenderService,
    ): Response
    {
        $this->isGranted(PermissionInterface::OWNER, $gallery);

        return $this->render('gallery/show.html.twig', [
            'content_header' => $galleryRenderService->getHeaderRenderDataForShow($gallery),
            'thumbnail_table_data' => $galleryRenderService->getTableRenderDataForShow($gallery),
            'gallery' => $gallery,
            'files' => $gallery->getFiles(),
            'commission' => $gallery->getCommission()
        ]);
    }

    #[Route('/{guid}/edit', name: 'app_gallery_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Gallery $gallery,
        EntityManagerInterface $entityManager,
        GalleryRenderService $galleryRenderService
    ): Response {
        $this->isGranted(PermissionInterface::OWNER, $gallery);

        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_gallery_show', ['guid' => $gallery->getGuid()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gallery/edit.html.twig', [
            'content_header' => $galleryRenderService->getHeaderRenderDataForEdit($gallery),
            'gallery' => $gallery,
            'form' => $form,
        ]);
    }

    #[Route('/{guid}', name: 'app_gallery_delete', methods: ['POST'])]
    public function delete(
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Gallery $gallery,
        GalleryService $galleryService
    ): Response {
        $this->isGranted(PermissionInterface::OWNER, $gallery);

        $galleryService->handleDeleteRequest($gallery);

        return $this->redirectToRoute('app_assigment_show', ['guid' => $gallery->getCommission()->getGuid()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/upload/{guid}', name: 'app_gallery_upload', methods: ['GET','POST'])]
    public function upload(
        Request $request,
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Gallery $gallery,
        GalleryRenderService $galleryRenderService
    ): Response  {
        $this->isGranted(PermissionInterface::OWNER, $gallery);

        $form = $this->createForm(UploadGalleryFilesType::class, $gallery);
        $form->handleRequest($request);

        return $this->render('gallery/upload.html.twig', [
            'content_header' => $galleryRenderService->getHeaderRenderDataForUpload($gallery),
            'gallery' => $gallery,
            'form' => $form,
        ]);
    }

    #[Route('/upload/{guid}/process', name: 'app_gallery_upload_process', methods: ['POST'])]
    public function process(
        Request $request,
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Gallery $gallery,
        UploadService $uploadService): JsonResponse {

        $this->isGranted(PermissionInterface::OWNER, $gallery);

        $form = $this->createForm(UploadGalleryFilesType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFiles = $form['files']->getData();

            if (empty($uploadedFiles)) {
                return new JsonResponse(['success' => false, 'message' => 'No files uploaded'], 400);
            }

            $uploadService->handleUploadRequest($form, $gallery);

            $results = [];
            foreach ($uploadedFiles as $uploadedFile) {
                // Process each file (in a real application, you'd save it to disk or cloud storage)
                $originalFilename = $uploadedFile->getClientOriginalName();
                $fileSize = $uploadedFile->getSize();

                $results[] = [
                    'filename' => $originalFilename,
                    'size' => $fileSize
                ];
            }

            return new JsonResponse([
                'success' => true,
                'message' => count($results) . ' file(s) uploaded successfully',
                'files' => $results
            ]);
        }

        return new JsonResponse(['success' => false, 'message' => 'Invalid form submission'], 400);
    }
}
