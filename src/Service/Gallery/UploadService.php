<?php

namespace App\Service\Gallery;

use App\Entity\Gallery;
use App\Repository\FileRepository;
use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use function Symfony\Component\Translation\t;

class UploadService
{

    public function __construct(
        private FileService $fileService,
        private FileRepository $fileRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function handleUploadRequest(FormInterface $form,  Gallery $gallery): void
    {
            /** @var array $files */
            $files = $form->get('files')->getData();

            $keyBase = $gallery->getGuid();
            $uplodedFiles = $this->fileService->uploadFiles($files, $keyBase);

            foreach ($uplodedFiles as $file) {
                $gallery->addFile($file);
            }

            $this->entityManager->persist($gallery);
            $this->entityManager->flush();
    }

    public function handleDeleteRequest(Gallery $gallery, string $fileGuid): void
    {
        $file = $this->fileRepository->findOneBy(['guid' => $fileGuid]);
        $this->fileService->removeFile($file);

        $gallery->removeFile($file);
        $file->setDeleted(true);

        $this->entityManager->persist($file);
        $this->entityManager->persist($gallery);
        $this->entityManager->flush();
    }


}