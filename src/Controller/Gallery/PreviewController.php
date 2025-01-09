<?php

namespace App\Controller\Gallery;

use App\Entity\Gallery;
use App\Repository\FavoriteRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/preview')]
class PreviewController extends AbstractController
{
    #[Route('/{guid}', name: 'app_gallery_preview', methods: ['GET'])]
    public function preview(
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Gallery $gallery
    )
    {
        return $this->render('gallery/preview.html.twig', [
            'gallery' => $gallery,
            'files' => $gallery->getFiles(),
        ]);
    }

    #[Route('/{guid}/data', name: 'app_preview_data')]
    public function previewData(
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Gallery $gallery,
        FavoriteRepository $favoriteRepository,
    ): JsonResponse
    {
        $user = $this->getUser();

        if (null !== $user) {
           $favorites = $favoriteRepository->findBy(['email' => $this->getUser()->getUserIdentifier(), 'gallery' => $gallery, 'deleted' => false]);
           $map = [];
           foreach ($favorites as $image) {
               $map[$image->getFile()->getGuid()] = $image;
           }
        }

        foreach ($gallery->getFiles() as $file) {
            $imageThumbnails[] = [
                'thumbnail_url' => $file->getThumbnailPath(),
                'image_url' => $file->getPath(),
                'image_alt' => $file->getName(),
                'is_vertical' => false,
                'cart_route' => 'app_dashboard',
                'cart_params' => ['id' => 1],
                'favorite_route' => 'app_dashboard',
                'favorite_params' => ['id' => 1],
                'guid' => $file->getGuid(),
                'gallery_guid' => $gallery->getGuid()
            ];
        }

        $renderedImages = [];
        foreach ($imageThumbnails as $image) {
            if (array_key_exists($image['guid'], $map)) {
                continue;
            }

            $renderedImages[] = $this->renderView('components/item-image.html.twig', ['image' => $image]);
        }

        return $this->json($renderedImages);
    }

}