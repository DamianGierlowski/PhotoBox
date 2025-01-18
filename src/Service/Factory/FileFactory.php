<?php

namespace App\Service\Factory;

use App\Entity\File;
use App\Util\GuidFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class FileFactory
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
    ) {
    }

    public function makeNewFile(string $fileName, string $path, string $thumbnailPath, string $mimeType, int $size, string $guid, ?string $watermarkPath): File
    {
        $file = new File();
        $file->setName($fileName)
            ->setPath($path)
            ->setMimeType($mimeType)
            ->setSize($size)
            ->setGuid($guid)
            ->setThumbnailPath($thumbnailPath)
            ->setWatermarkPath($watermarkPath)
            ->setCreatedBy($this->security->getUser())
        ;

        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return $file;
    }
}