<?php

namespace App\Service;

use App\Entity\File;
use App\Service\Factory\FileFactory;
use App\Util\ArchiveClient;
use App\Util\GuidFactory;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{

    private const ORIGINAL_CATALOG = 'originals';
    private const THUMBNAIL_CATAOLG = 'thumbnails';

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

        $thumbnailPath = '/var/tmp/' . $file->getClientOriginalPath();

        $fileThumbnailContent =  $this->createThumbnail($fileContent, $thumbnailPath, 200, 200);
        $fileMimeType = $file->getMimeType();

        $guid = GuidFactory::generate();
        $keyOriginal = $keyBase . '/' . self::ORIGINAL_CATALOG . '/' . $guid;
        $keyThumbnail = $keyBase . '/' . self::THUMBNAIL_CATAOLG . '/' . $guid;

        $this->archiveClient->uploadFile($keyOriginal, $fileContent, $fileMimeType);
        $this->archiveClient->uploadFile($keyThumbnail, $fileThumbnailContent, $fileMimeType);

        return $this->fileFactory->makeNewFile($file->getClientOriginalPath(), $keyOriginal, $keyThumbnail, $fileMimeType, $file->getSize(), $guid);
    }

    public function removeFile(File $file): void
    {
        $this->archiveClient->deleteFile($file->getPath());
    }

    private function createThumbnail(string $originalPath, string $thumbnailPath, int $width, int $height): string
    {
        $imagine = new Imagine();
        $imagine
            ->load($originalPath)
            ->thumbnail(new Box($width, $height))
            ->save($thumbnailPath, ['quality' => 80]);

        return file_get_contents($thumbnailPath);
    }
}