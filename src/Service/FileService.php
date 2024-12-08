<?php

namespace App\Service;

use App\Entity\File;
use App\Entity\Gallery;
use App\Service\Factory\FileFactory;
use App\Util\ArchiveClient;
use App\Util\GuidFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{

    public function __construct(
        private ArchiveClient $archiveClient,
        private FileFactory $fileFactory,
    ) {
    }

    public function uploadFiles(array $files, Gallery $gallery): array
    {
        $result = [];
        foreach ($files as $file) {
          $result[] = $this->uploadSingleFile($file, $gallery);
        }

        return $result;
    }

    public function uploadSingleFile(UploadedFile $file, Gallery $gallery): File
    {
        $fileContent = file_get_contents($file->getPathname());
        $fileMimeType = $file->getMimeType();
        $key = $gallery->getGuid() . '/' . GuidFactory::generate();
        $this->archiveClient->uploadFile($key, $fileContent, $fileMimeType);

        return $this->fileFactory->makeNewFile($file->getClientOriginalPath(), $key, $fileMimeType, $file->getSize(), $gallery);
    }
}