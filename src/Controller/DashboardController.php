<?php

namespace App\Controller;

use App\Command\WidgetService;
use App\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{

    #[IsGranted("ROLE_USER")]
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        WidgetService $widgetService
    ): Response
    {
        $stats = [
            'active_commissions' => $widgetService->getActiveCommissionWidgetData($this->getUser()),
//            'revenue' => '', //TODO when commissions ready
            'total_galleries' => $widgetService->getActiveGalleryWidgetData($this->getUser()),
            'storage_used' => $widgetService->getStorageWidgetData($this->getUser()) .' GB',
        ];
        $recentCommissions = $widgetService->getRecentCommissions($this->getUser());
        $incomingCommissions = $widgetService->getIncomingCommissions($this->getUser());

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
            'recent_commissions' => $recentCommissions,
            'incoming_commissions' => $incomingCommissions,
        ]);
    }
}
