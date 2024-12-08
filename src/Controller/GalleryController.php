<?php

namespace App\Controller;

use App\Entity\Assigment;
use App\Entity\Gallery;
use App\Form\Gallery\UploadGalleryFilesType;
use App\Form\GalleryType;
use App\Repository\GalleryRepository;
use App\Service\FileService;
use App\Util\ArchiveClient;
use App\Util\GuidFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gallery')]
final class GalleryController extends AbstractController
{

    #[Route('/new/{guid}', name: 'app_gallery_new', methods: ['GET', 'POST'])]

    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gallery = new Gallery();
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $guid = $request->get('guid');
            $assigment = $entityManager->getRepository(Assigment::class)->findOneBy(['guid' => $guid]);

            $gallery->setAssigment($assigment);
            $gallery->setGuid(GuidFactory::generate());

            $entityManager->persist($gallery);
            $entityManager->flush();

            return $this->redirectToRoute('app_assigment_show', ['id' => $assigment->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gallery/new.html.twig', [
            'gallery' => $gallery,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gallery_show', methods: ['GET'])]
    public function show(Gallery $gallery): Response
    {
        return $this->render('gallery/show.html.twig', [
            'gallery' => $gallery,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gallery_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Gallery $gallery, EntityManagerInterface $entityManager, FileService $fileService): Response
    {
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
//            /** @var UploadedFile $brochure */
//            $files = $form->get('brochure')->getData();
//            $fileService->uploadFiles($files, $gallery);

            $entityManager->flush();

            return $this->redirectToRoute('app_assigment_show', ['id' => $gallery->getAssigment()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gallery/edit.html.twig', [
            'gallery' => $gallery,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gallery_delete', methods: ['POST'])]
    public function delete(Request $request, Gallery $gallery, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gallery->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($gallery);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_gallery_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/upload/{id}', name: 'app_gallery_upload', methods: ['GET','POST'])]
    public function addFiles(Request $request, Gallery $gallery): Response
    {
        $form = $this->createForm(UploadGalleryFilesType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            return $this->redirectToRoute('app_gallery_show', ['id' => $gallery->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gallery/upload.html.twig', [
            'gallery' => $gallery,
            'form' => $form,
        ]);
    }
}
