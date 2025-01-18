<?php

namespace App\Service;

use App\Entity\File;
use App\Service\Factory\FileFactory;
use App\Service\Files\WatermarkingService;
use App\Util\ArchiveClient;
use App\Util\GuidFactory;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class FileService
{

    private const ORIGINAL_CATALOG = 'originals';
    private const THUMBNAIL_CATALOG = 'thumbnails';
    private const WATERMARK_CATALOG = 'watermarked';

    public function __construct(
        private ArchiveClient $archiveClient,
        private FileFactory $fileFactory,
        private WatermarkingService $watermarkingService,
    ) {
    }

    public function uploadFiles(array $files, string $keyBase, bool $watermark = false): array
    {
        $result = [];
        /** @var UploadedFile $file */
        foreach ($files as $file) {
          $result[] = $this->uploadSingleFile($file, $keyBase, $watermark);
        }

        return $result;
    }

    public function uploadSingleFile(UploadedFile $file, string $keyBase, bool $watermark = false): File
    {

        $fileContent = file_get_contents($file->getPathname());
        $thumbnailPath = '/var/tmp/' . $file->getClientOriginalPath();
        $fileThumbnailWatermarkedContent = '';
        $guid = GuidFactory::generate();

        if ($watermark) {
            $this->watermarkingService->addWatermark($file->getPathname(), 'Preview', $file->getPathname().'_watermarked');
            $fileThumbnailWatermarkedContent =  file_get_contents($file->getPathname().'_watermarked');
            $keyWatermarked = $keyBase . '/' . self::WATERMARK_CATALOG . '/' . $guid;
        }

        $fileThumbnailContent = $this->createThumbnail($fileContent, $thumbnailPath, 800, 800);
        $fileMimeType = $file->getMimeType();

        $guid = GuidFactory::generate();
        $keyOriginal = $keyBase . '/' . self::ORIGINAL_CATALOG . '/' . $guid;
        $keyThumbnail = $keyBase . '/' . self::THUMBNAIL_CATALOG . '/' . $guid;

        $file = $this->fileFactory->makeNewFile($file->getClientOriginalPath(), $keyOriginal, $keyThumbnail, $fileMimeType, $file->getSize(), $guid, $keyWatermarked ?? null);

        try {
            $this->archiveClient->uploadFile($keyOriginal, $fileContent, $fileMimeType);
            $this->archiveClient->uploadFile($keyThumbnail, $fileThumbnailContent, $fileMimeType);
            if ( '' !== $fileThumbnailWatermarkedContent) {
                $this->archiveClient->uploadFile($keyWatermarked, $fileThumbnailWatermarkedContent, $fileMimeType);
            }
        } catch (\Exception $exception) {
            $this->removeFile($file);

            throw new $exception;
        }

        return $file;
    }

    public function removeFile(File $file): void
    {
        $this->archiveClient->deleteFile($file->getPath());
        $this->archiveClient->deleteFile($file->getThumbnailPath());

        if (!empty($file->getWatermarkPath())) {
            $this->archiveClient->deleteFile($file->getWatermarkPath());
        }
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