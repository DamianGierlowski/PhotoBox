<?php

namespace App\Controller;

use App\Service\Factory\ComponentFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{

    public function __construct(
        private ComponentFactory $componentFactory
    )
    {

    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
       $builderHeader = $this->componentFactory->create(ComponentFactory::CONTENT_HEADER);

       $builderHeader
           ->addTitle('Dashboard')
           ->addBackLink('app_dashboard')
           ->addButton('app_dashboard', 'mdi:arrow-left')
       ;
        $contentHeaderData = $builderHeader->build();

        $tableData = [
            'headers' => [
                ['key' => 'id', 'label' => 'ID'],
                ['key' => 'name', 'label' => 'Name'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'amount', 'label' => 'Amount'],
                ['key' => 'date', 'label' => 'Date'],
            ],
            'rows' => [
              2 =>  [
                    'id' => 1,
                    'name' => 'John Doe',
                    'status' => ['value' => 'Active', 'color' => 'green'],
                    'amount' => '$100.00',
                    'date' => '2023-05-15',
                ],
                24 =>  [
                    'id' => 2,
                    'name' => 'Jane Smith',
                    'status' => ['value' => 'Inactive', 'color' => 'red'],
                    'amount' => ['value' => '$75.50', 'color' => 'blue'],
                    'date' => '2023-05-14',
                ],
                25 =>  [
                    'id' => 3,
                    'name' => 'Bob Johnson',
                    'status' => ['value' => 'Pending', 'color' => 'yellow'],
                    'amount' => '$200.00',
                    'date' => '2023-05-13',
                ],
            ],
            'actions' => [
              1 =>  [
                    'route' => 'app_dashboard',
                    'params' => ['id' => 'id', 'name' => 'name'],
                    'icon' => 'lucide:aperture',
                ],
                2 =>  [
                    'route' => 'app_dashboard',
                    'params' => ['id' => 'id'],
                    'icon' => 'lucide:aperture',
                ],
                [
                    'route' => 'app_dashboard',
                    'params' => ['id' => 'id', 'status' => 'status.value'],
                    'icon' => 'lucide:aperture',
                    'class' => 'text-red-600 hover:text-red-900',
                ],
            ],
        ];

        $imageThumbnails = [
            [
                'image_url' => 'https://picsum.photos/400/300?random=1',
                'image_alt' => 'Sample Image 1',
                'preview_route' => 'app_dashboard',
                'preview_params' => ['id' => 1],
                'is_vertical' => false,
                'cart_route' => 'app_dashboard',
                'cart_params' => ['id' => 1],
                'favorite_route' => 'app_dashboard',
                'favorite_params' => ['id' => 1],
            ],
            [
                'image_url' => 'https://picsum.photos/400/600?random=2',
                'image_alt' => 'Sample Image 2',
                'is_vertical' => true,
                'preview_route' => 'app_dashboard',
                'preview_params' => ['id' => 2],
                'cart_route' => 'app_dashboard',
                'cart_params' => ['id' => 2],
                'favorite_route' => 'app_dashboard',
                'favorite_params' => ['id' => 2],
            ],
        ];

        $thumbnailTableData = [
            'headers' => [
                ['key' => 'thumbnail', 'label' => 'Image'],
                ['key' => 'name', 'label' => 'Name'],
                ['key' => 'description', 'label' => 'Description'],
                ['key' => 'price', 'label' => 'Price'],
            ],
            'rows' => [
                [
                    'id' => 1,
                    'name' => 'Product 1',
                    'description' => 'Description for Product 1',
                    'price' => '$19.99',
                    'image_url' => 'https://picsum.photos/200/200?random=1',
                ],
                [
                    'id' => 2,
                    'name' => 'Product 2',
                    'description' => 'Description for Product 2',
                    'price' => '$29.99',
                    'image_url' => 'https://picsum.photos/200/200?random=2',
                    'thumbnail_url' => 'https://picsum.photos/200/200?random=2',
                ],
                [
                    'id' => 3,
                    'name' => 'Product 3',
                    'description' => 'Description for Product 3',
                    'price' => '$39.99',
                    'image_url' => 'https://picsum.photos/200/200?random=3',
                ],
            ],
            'thumbnail_config' => [
                'image_url_key' => 'image_url',
                'thumbnail_url_key' => 'thumbnail_url',
                'image_alt_key' => 'name',
            ],
            'actions' => [
                [
                    'route' => 'app_dashboard',
                    'params' => ['id' => 'id'],
                    'icon' => 'mingcute:pencil-fill',
                ],
                [
                    'route' => 'app_dashboard',
                    'params' => ['id' => 'id'],
                    'icon' => 'mdi:trash',
                    'class' => 'text-red-600 hover:text-red-900',
                ],
            ],
        ];

        // Add a flash message for demonstration
        $this->addFlash('success', 'Welcome to the dashboard!');

        return $this->render('dashboard/index.html.twig', [
            'content_header' => $contentHeaderData,
            'table_data' => $tableData,
            'image_thumbnails' => $imageThumbnails,
            'thumbnail_table_data' => $thumbnailTableData,
        ]);
    }
}
