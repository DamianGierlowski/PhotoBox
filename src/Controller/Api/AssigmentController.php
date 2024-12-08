<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\Api\Assigment\AssigmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

#[Route('/api/assigment')]
#[IsGranted('ROLE_USER')]
class AssigmentController extends AbstractController
{

    #[Route('/', name: 'app_api_assigment_index', methods: ['GET'])]
    public function index(
        Request $request,
        AssigmentService $assigmentService,
    ): JsonResponse {

        try {
            $data = $assigmentService->handleIndexRequest($this->getUser());
        } catch (\Throwable $throwable) {
            return new JsonResponse(['message' => 'An error occured ']); //TODO better error message
        }

        return new JsonResponse($data, 200);
    }

    #[Route('/new', name: 'app_api_assigment_new', methods: ['POST'])]
    public function new(
        Request $request,
        AssigmentService $assigmentService,
    ): JsonResponse
    {
        try {
           $assigmentService->handleNewRequest($request, $this->getUser());
        } catch (\Throwable $throwable) {

            return new JsonResponse(['message' => 'An error occured ']); //TODO better error message
        }

        return new JsonResponse(['message' => 'Created and assigment'], 200);
    }

    #[Route('/edit', name: 'app_api_assigment_edit', methods: ['POST'])]
    public function edit(
        Request $request,
        AssigmentService $assigmentService,
    ): JsonResponse
    {
        try {
            $assigmentService->handleEditRequest($request, $this->getUser());
        } catch (\Throwable $throwable) {

            return new JsonResponse(['message' => 'An error occured ']); //TODO better error message
        }

        return new JsonResponse(['message' => 'Edited an assigment'], 200);
    }

    #[Route('/delete/{guid}', name: 'app_api_assigment_delete', methods: ['DELETE'])]
    public function delete(
        string $guid,
        Request $request,
        AssigmentService $assigmentService,
    ): JsonResponse
    {
        try {
            $assigmentService->handleDeleteRequest($guid, $this->getUser());
        } catch (Throwable $throwable) {

            return new JsonResponse(['message' => 'An error occured ']); //TODO better error message
        }

        return new JsonResponse(['message' => 'Deleted an assigment'], 200);
    }


}