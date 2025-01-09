<?php

namespace App\Service\Gallery;

use App\Entity\Favorite;
use App\Entity\File;
use App\Entity\Gallery;
use App\Repository\FavoriteRepository;
use App\Repository\FileRepository;
use App\Repository\GalleryRepository;
use App\Util\GuidFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class FavoritesService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private FileRepository $fileRepository,
        private GalleryRepository $galleryRepository,
        private FavoriteRepository $favoriteRepository,
    ) {
    }

    private function handleRequest(Request $request): array
    {
        $data = (array) json_decode($request->getContent());

        if (!array_key_exists('fileGuid', $data)) {
            throw new \Exception("File guid is missing", 400);
        }

        if (!array_key_exists('galleryGuid', $data)) {
            throw new \Exception("galleryGuid is missing", 400);
        }

        if (!array_key_exists('email', $data)) {
            throw new \Exception("email is missing", 400);
        }

        return $data;
    }

    public function handleList(Request $request, UserInterface $user): array
    {
        $data = $this->handleRequest($request);

        $gallery = $this->galleryRepository->findOneByGuid($data['galleryGuid']);
        $favorites = $this->favoriteRepository->findBy(['gallery' => $gallery, 'email' => $user->getUserIdentifier(), 'deleted' => false]);

        $imageThumbnails = [];

        foreach ($favorites as $favorite) {
            $imageThumbnails[] = [
                'thumbnail_url' => $favorite->getFile()->getThumbnailPath(),
                'image_url' => $favorite->getFile()->getPath(),
                'image_alt' => $favorite->getFile()->getName(),
                'is_vertical' => false,
                'cart_route' => 'app_dashboard',
                'cart_params' => ['id' => 1],
                'favorite_route' => 'app_dashboard',
                'favorite_params' => ['id' => 1],
                'guid' => $favorite->getFile()->getGuid(),
                'gallery_guid' => $favorite->getGallery()->getGuid()
            ];
        }

        return $imageThumbnails;
    }

    public function handleAddFavorite(Request $request, UserInterface $user): void
    {
        $data = $this->handleRequest($request);

        $file = $this->fileRepository->findOneByGuid($data['fileGuid']);
        $gallery = $this->galleryRepository->findOneByGuid($data['galleryGuid']);

        $favorite = new Favorite();
        $favorite
            ->setFile($file)
            ->setGallery($gallery)
            ->setEmail($user->getUserIdentifier())
            ->setGuid(GuidFactory::generate())
        ;

        $this->entityManager->persist($favorite);
        $this->entityManager->flush();
    }

    public function handleRemoveFavorite(Request $request, UserInterface $user): void
    {
        $data = $this->handleRequest($request);

        $file = $this->fileRepository->findOneByGuid($data['fileGuid']);
        $gallery = $this->galleryRepository->findOneByGuid($data['galleryGuid']);

        $favorite = $this->favoriteRepository->findOneBy(['file' => $file, 'gallery' => $gallery, 'email' => $user->getUserIdentifier(), 'deleted' => false]);

        if (null === $favorite) {
            throw new \Exception("Favorite not found", 400);
        }

        $favorite->setDeleted(true);

        $this->entityManager->persist($favorite);
        $this->entityManager->flush();
    }
}