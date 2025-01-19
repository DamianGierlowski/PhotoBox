<?php

namespace App\Command;

use App\Repository\CommissionRepository;
use App\Repository\FileRepository;
use App\Repository\GalleryRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class WidgetService
{
    public function __construct(
        private FileRepository $fileRepository,
        private CommissionRepository $commissionRepository,
        private GalleryRepository $galleryRepository,
    ) {
    }

    // Returns storage size in GB
    public function getStorageWidgetData(UserInterface $user): float
    {
        return round($this->fileRepository->getTotalSize($user) / 1024 , 2);
    }

    public function getActiveCommissionWidgetData(UserInterface $user): int
    {
        return $this->commissionRepository->getTotalActiveCommissionsForUser($user);
    }

    public function getActiveGalleryWidgetData(UserInterface $user): int
    {
        return $this->galleryRepository->getTotalActiveGalleryForUser($user);
    }

    public function getRecentCommissions(UserInterface $user): array
    {
        return $this->commissionRepository->findRecentCommissionsForUser($user);
    }

    public function getIncomingCommissions(UserInterface $user): array
    {
        return $this->commissionRepository->findIncomingCommissionsForUser($user, 4);
    }
}