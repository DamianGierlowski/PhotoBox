<?php

namespace App\Controller\Gallery;

use App\Entity\Assigment;
use App\Entity\Gallery;
use App\Form\Gallery\UploadGalleryFilesType;
use App\Form\GalleryType;
use App\Service\Gallery\GalleryService;
use App\Service\Gallery\UploadService;
use App\UniqueNameInterface\PermissionInterface;
use App\Util\GuidFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gallery')]
final class GalleryController extends AbstractController
{

    #[Route('/new/{guid}', name: 'app_gallery_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Assigment $assigment,
        EntityManagerInterface $entityManager): Response
    {
        $gallery = new Gallery();
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gallery->setAssigment($assigment);
            $gallery->setGuid(GuidFactory::generate());

            $entityManager->persist($gallery);
            $entityManager->flush();

            return $this->redirectToRoute('app_assigment_show', ['guid' => $assigment->getGuid()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gallery/new.html.twig', [
            'gallery' => $gallery,
            'form' => $form,
        ]);
    }

    #[Route('/{guid}', name: 'app_gallery_show', methods: ['GET'])]
    public function show(
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Gallery $gallery
    ): Response
    {
        return $this->render('gallery/show.html.twig', [
            'gallery' => $gallery,
            'files' => $gallery->getFiles(),
            'assigment' => $gallery->getAssigment()
        ]);
    }

    #[Route('/{guid}/edit', name: 'app_gallery_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Gallery $gallery,
        EntityManagerInterface $entityManager): Response
    {
        $this->isGranted(PermissionInterface::OWNER, $gallery);

        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_assigment_show', ['guid' => $gallery->getAssigment()->getGuid()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gallery/edit.html.twig', [
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

        return $this->redirectToRoute('app_assigment_show', ['guid' => $gallery->getAssigment()->getGuid()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/upload/{guid}', name: 'app_gallery_upload', methods: ['GET','POST'])]
    public function upload(
        Request $request,
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Gallery $gallery,
        UploadService $uploadService
    ): Response  {
        $this->isGranted(PermissionInterface::OWNER, $gallery);

        $form = $this->createForm(UploadGalleryFilesType::class, $gallery);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            foreach ($form->getErrors(true) as $error){
                $this->addFlash('error', $error->getMessage());
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadService->handleUploadRequest($form, $gallery);

            return $this->redirectToRoute('app_gallery_show', ['guid' => $gallery->getGuid()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gallery/upload.html.twig', [
            'gallery' => $gallery,
            'form' => $form,
        ]);
    }
}
