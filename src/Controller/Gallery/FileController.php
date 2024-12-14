<?php

namespace App\Controller\Gallery;

use App\Entity\Gallery;
use App\Service\Gallery\UploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gallery')]
class FileController extends AbstractController
{
    #[Route('{id}/file/delete/{guid}', name: 'app_gallery_file_delete')]
    public function delete(Request $request, Gallery $gallery, string $guid, UploadService $uploadService)
    {
        $uploadService->handleDeleteRequest($gallery, $guid);

        return $this->redirectToRoute('app_gallery_show', ['id' => $gallery->getId()]);
    }
}