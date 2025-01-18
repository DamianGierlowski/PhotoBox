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
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
                'image_url' => $file->getWatermarkPath() ?? $file->getPath(),
                'alt' => $file->getName(),
                'guid' => $file->getGuid(),
                'gallery_guid' => $gallery->getGuid(),
                'gridClass' => 'col-span-2 row-span-2',
            ];
        }

        $images = [
            [
                'image_url' => 'https://picsum.photos/400/300?random=1',
                'thumbnail_url' => 'https://picsum.photos/400/300?random=1',
                'alt' => 'Gallery Image 1',
                'gridClass' => 'col-span-2 row-span-2',
                'aspectRatio' => '4/3'
            ],
            [
                'image_url' => 'https://picsum.photos/800/600?random=2',
                'thumbnail_url' => 'https://picsum.photos/800/600?random=2',
                'alt' => 'Gallery Image 2',
                'gridClass' => 'col-span-2 row-span-2',
                'aspectRatio' => '4/3'
            ],
            [
                'image_url' => 'https://picsum.photos/400/300?random=3',
                'thumbnail_url' => 'https://picsum.photos/400/300?random=3',
                'alt' => 'Gallery Image 3',
                'gridClass' => 'col-span-2 row-span-1',
                'aspectRatio' => '4/3'
            ],
            [
                'image_url' => 'https://picsum.photos/400/600?random=4',
                'thumbnail_url' => 'https://picsum.photos/400/600?random=4',
                'alt' => 'Gallery Image 4',
                'gridClass' => 'row-span-2',
                'aspectRatio' => '2/3'
            ],
            [
                'image_url' => 'https://picsum.photos/400/300?random=5',
                'thumbnail_url' => 'https://picsum.photos/400/300?random=5',
                'alt' => 'Gallery Image 5',
                'gridClass' => 'col-span-1 row-span-1',
                'aspectRatio' => '4/3'
            ],
            [
                'image_url' => 'https://picsum.photos/800/300?random=6',
                'thumbnail_url' => 'https://picsum.photos/800/300?random=6',
                'alt' => 'Gallery Image 6',
                'gridClass' => 'col-span-1 row-span-1',
                'aspectRatio' => '4/3'
            ],[
                'image_url' => 'https://picsum.photos/800/300?random=7',
                'thumbnail_url' => 'https://picsum.photos/800/300?random=7',
                'alt' => 'Gallery Image 6',
                'gridClass' => 'col-span-1 row-span-1',
                'aspectRatio' => '4/3'
            ]
        ];


        return $this->json([
            'html' => $this->renderView('components/gallery-grid.html.twig', ['images' => $imageThumbnails])
        ]);

//        $renderedImages = [];
//        foreach ($imageThumbnails as $image) {
//            if (array_key_exists($image['guid'], $map)) {
//                continue;
//            }
//
//            $renderedImages[] = $this->renderView('components/gallery_grid.html.twig', ['images' => $image]);
//        }
//
//        return $this->json($renderedImages);
    }

}