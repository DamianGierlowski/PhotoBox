<?php

namespace App\Controller\Gallery;

use App\Entity\Gallery;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gallery')]
class PreviewController extends AbstractController
{

    #[Route('/{guid}/preview', name: 'app_gallery_preview', methods: ['GET'])]
    public function preview(
        #[MapEntity(mapping: ['guid' => 'guid'])]
        Gallery $gallery
    )
    {


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
            ];
        }


        return $this->render('gallery/preview.html.twig', [
            'gallery' => $gallery,
            'files' => $gallery->getFiles(),
            'image_thumbnails' => $imageThumbnails,
        ]);
    }
}