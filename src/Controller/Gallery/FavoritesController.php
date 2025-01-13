<?php

namespace App\Controller\Gallery;


use App\Service\Gallery\FavoritesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/favorites')]
class FavoritesController extends AbstractController
{

    #[IsGranted('ROLE_GUEST')]
    #[Route('/',name:'app_favorites_list')]
    public function list(Request $request, FavoritesService $favoritesService)
    {
        $user = $this->getUser();

        if (null === $user) {
            return $this->json([]);
        }

        $imageThumbnails = $favoritesService->handleList($request, $user);

        $renderedImages = [];
        foreach ($imageThumbnails as $image) {
            $renderedImages[] = $this->renderView('components/item-image-favorite.html.twig', ['image' => $image]);
        }

        return $this->json($renderedImages);
    }

    #[IsGranted('ROLE_GUEST')]
    #[Route('/add')]
    public function add(Request $request, FavoritesService $favoritesService): JsonResponse
    {
        try {
            $favoritesService->handleAddFavorite($request, $this->getUser());
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], $exception->getCode());
        }

        return new JsonResponse(['message' => "Successfully added"]);
    }

    #[IsGranted('ROLE_GUEST')]
    #[Route('/remove')]
    public function remove(Request $request, FavoritesService $favoritesService): JsonResponse
    {
        try {
            $favoritesService->handleRemoveFavorite($request, $this->getUser());
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], $exception->getCode());
        }

        return new JsonResponse(['message' => "Successfully removed"]);
    }
}