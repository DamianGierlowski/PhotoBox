<?php

namespace App\Service\Factory;

use App\Entity\File;
use App\Entity\Gallery;
use Doctrine\ORM\EntityManagerInterface;

class FileFactory
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function makeNewFile(string $fileName, string $path, string $mimeType, int $size, Gallery $gallery)
    {
        $file = new File();
        $file->setFileName($fileName)
            ->setPath($path)
            ->setMimeType($mimeType)
            ->setSize($size)
            ->setGallery($gallery)
        ;

        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return $file;
    }
}