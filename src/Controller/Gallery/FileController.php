<?php

namespace App\Controller\Gallery;

use App\Entity\File;
use App\Entity\Gallery;
use App\Service\Gallery\UploadService;
use App\UniqueNameInterface\PermissionInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gallery')]
class FileController extends AbstractController
{
    #[Route('{guid}/file/delete/{guid_file}', name: 'app_gallery_file_delete')]
    public function delete(
        Request $request,
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Gallery $gallery,
        #[MapEntity(mapping: ['guid_file' => 'guid'])]
        File $file,
        UploadService $uploadService)
    {
        $this->isGranted(PermissionInterface::OWNER, $gallery);

        $uploadService->handleDeleteRequest($gallery, $file);

        return $this->redirectToRoute('app_gallery_show', ['id' => $gallery->getId()]);
    }
}