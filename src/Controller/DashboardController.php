<?php

namespace App\Controller;

use App\Service\Factory\ComponentFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{

    #[IsGranted("ROLE_USER")]
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        // Dummy data for demonstration
        $stats = [
            'active_commissions' => 12,
            'revenue' => '$5,230',
            'total_galleries' => 45,
            'storage_used' => '2.3 GB / 10 GB',
        ];

        $recentCommissions = [
            ['date' => '2023-05-15', 'name' => 'Wedding Photoshoot', 'status' => 'In Progress', 'income' => '$1,200'],
            ['date' => '2023-05-12', 'name' => 'Product Catalog', 'status' => 'Completed', 'income' => '$800'],
            ['date' => '2023-05-10', 'name' => 'Family Portrait', 'status' => 'Pending', 'income' => '$350'],
            ['date' => '2023-05-08', 'name' => 'Corporate Event', 'status' => 'In Progress', 'income' => '$2,000'],
        ];

        $incomingCommissions = [
            ['name' => 'Beach Wedding', 'date' => '2023-06-01'],
            ['name' => 'Fashion Show', 'date' => '2023-06-05'],
            ['name' => 'Real Estate Listing', 'date' => '2023-06-10'],
            ['name' => 'Graduation Ceremony', 'date' => '2023-06-15'],
        ];

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
            'recent_commissions' => $recentCommissions,
            'incoming_commissions' => $incomingCommissions,
        ]);
    }
}
