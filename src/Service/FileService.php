<?php

namespace App\Service;

use App\Entity\File;
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

    public function uploadFiles(array $files, string $keyBase): array
    {
        $result = [];
        /** @var UploadedFile $file */
        foreach ($files as $file) {
          $result[] = $this->uploadSingleFile($file, $keyBase);
        }

        return $result;
    }

    public function uploadSingleFile(UploadedFile $file, string $keyBase): File
    {
        $fileContent = file_get_contents($file->getPathname());
        $fileMimeType = $file->getMimeType();

        $guid = GuidFactory::generate();
        $key = $keyBase . '/' . $guid;
        $this->archiveClient->uploadFile($key, $fileContent, $fileMimeType);

        return $this->fileFactory->makeNewFile($file->getClientOriginalPath(), $key, $fileMimeType, $file->getSize(), $guid);
    }

    public function removeFile(File $file): void
    {
        $this->archiveClient->deleteFile($file->getPath());
    }
}