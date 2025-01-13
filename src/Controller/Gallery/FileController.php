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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/gallery')]
class FileController extends AbstractController
{
    #[Route('{gallery_guid}/file/delete/{guid_file}', name: 'app_gallery_file_delete')]
    public function delete(
        Request $request,
        #[MapEntity(mapping: ['gallery_guid' => 'guid'])]
        Gallery $gallery,
        #[MapEntity(mapping: ['guid_file' => 'guid'])]
        File $file,
        UploadService $uploadService)
    {
        $this->isGranted(PermissionInterface::OWNER, $gallery);

        $uploadService->handleDeleteRequest($gallery, $file);

        return $this->redirectToRoute('app_gallery_show', ['guid' => $gallery->getGuid()]);
    }
}