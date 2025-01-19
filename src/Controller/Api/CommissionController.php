<?php

namespace App\Controller\Api;


use App\Repository\CommissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
#[Route('/api/commission')]
class CommissionController  extends AbstractController
{
    #[Route('/search', name: 'app_api_commission_search', methods: ['GET'])]
    public function search(Request $request,  CommissionRepository $commissionRepository): JsonResponse
    {
        $title = $request->query->get('title');
        $date = $request->query->get('date');

        return $this->json([
            'commissions' => $commissionRepository->findCommissionByTitleAndDateForUser($this->getUser(), $title, $date),
            'title' => $title,
            'date' => $date,
        ]);
    }
}