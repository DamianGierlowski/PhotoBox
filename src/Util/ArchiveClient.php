<?php

namespace App\Util;

use Aws\Exception\AwsException;
use Aws\S3\S3Client;

class ArchiveClient
{
    private S3Client $s3Client;
    private string $bucketName;

    public function __construct(string $endpoint, string $accessKey, string $secretKey, string $bucketName)
    {
        $this->bucketName = $bucketName;

        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => 'us-east-1',
            'endpoint' => $endpoint,
            'credentials' => [
                'key' => $accessKey,
                'secret' => $secretKey,
            ],
            'use_path_style_endpoint' => true,
        ]);
    }

    public function uploadFile(string $key, string $fileContent, string $mimeType): void
    {
        try {
            $this->s3Client->putObject([
                'Bucket' => $this->bucketName,
                'Key'    => $key,
                'Body' => $fileContent,
                'ContentType' => $mimeType,
            ]);
        } catch (AwsException $e) {
            throw new \RuntimeException('Error uploading file: ' . $e->getMessage());
        }
    }

    public function getFileUrl(string $key): string
    {
        return $this->s3Client->getObjectUrl($this->bucketName, $key);
    }

    public function deleteFile(string $key): void
    {
        try {
            $this->s3Client->deleteObject([
                'Bucket' => $this->bucketName,
                'Key'    => $key,
            ]);
        } catch (AwsException $e) {
            throw new \RuntimeException('Error deleting file: ' . $e->getMessage());
        }
    }



}