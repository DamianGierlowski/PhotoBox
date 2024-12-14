<?php

namespace App\Service\Gallery;

use App\Entity\Gallery;
use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\Translation\t;

class GalleryService
{
    public function __construct(
        private FileService $fileService,
        private EntityManagerInterface $entityManager,
    )
    {

    }
    public function handleDeleteRequest(Gallery $gallery)
    {
        $files = $gallery->getFiles();

        foreach ($files as $file) {
            $this->fileService->removeFile($file);
            $file->setDeleted(true);

            $this->entityManager->persist($file);
        }

        $gallery->setDeleted(true);

        $this->entityManager->persist($gallery);
        $this->entityManager->flush();
    }

    private function removeFile()
    {

    }
}