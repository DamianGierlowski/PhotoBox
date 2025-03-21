<?php

namespace App\Service\Gallery;

use App\Entity\File;
use App\Entity\Gallery;

use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
class UploadService
{

    public function __construct(
        private FileService $fileService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function handleUploadRequest(FormInterface $form,  Gallery $gallery): void
    {
            /** @var array $files */
            $files = $form->get('files')->getData();
            $watermark = $form->get('watermark')->getData();

            $keyBase = $gallery->getGuid();
            $uplodedFiles = $this->fileService->uploadFiles($files, $keyBase, $watermark);

            foreach ($uplodedFiles as $file) {
                $gallery->addFile($file);
            }

            $this->entityManager->persist($gallery);
            $this->entityManager->flush();
    }

    public function handleDeleteRequest(Gallery $gallery, File $file): void
    {
        $this->fileService->removeFile($file);

        $gallery->removeFile($file);
        $file->setDeleted(true);

        $this->entityManager->persist($file);
        $this->entityManager->persist($gallery);
        $this->entityManager->flush();
    }

}