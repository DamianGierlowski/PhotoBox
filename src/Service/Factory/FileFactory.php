<?php

namespace App\Service\Factory;

use App\Entity\File;
use App\Util\GuidFactory;
use Doctrine\ORM\EntityManagerInterface;

class FileFactory
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function makeNewFile(string $fileName, string $path, string $mimeType, int $size, string $guid): File
    {
        $file = new File();
        $file->setName($fileName)
            ->setPath($path)
            ->setMimeType($mimeType)
            ->setSize($size)
            ->setGuid($guid)
        ;

        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return $file;
    }
}