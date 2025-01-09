<?php

namespace App\Service\Commission;

use App\Entity\Commission;
use App\Repository\CommissionRepository;
use App\Service\Factory\ComponentFactory;
use Symfony\Component\Security\Core\User\UserInterface;

class CommissionRenderService
{

    public function __construct(
        private ComponentFactory     $componentFactory,
        private CommissionRepository $assigmentRepository,
    ) {
    }

    public function getHeaderRenderDataForIndex(): array
    {
        $builderHeader = $this->componentFactory->create(ComponentFactory::CONTENT_HEADER);

        $builderHeader
            ->addTitle('commission.header_title')
            ->addBackLink('app_dashboard')
            ->addButton('app_commission_new', 'mono-icons:add')
        ;

        return $builderHeader->build();
    }

    public function getHeaderRenderDataForForms(): array
    {
        $builderHeader = $this->componentFactory->create(ComponentFactory::CONTENT_HEADER);

        $builderHeader
            ->addTitle('commission.new_title')
            ->addBackLink('app_commission_index')
        ;

        return $builderHeader->build();
    }

    public function getHeaderRenderDataForShow(Commission $commission): array
    {
        $builderHeader = $this->componentFactory->create(ComponentFactory::CONTENT_HEADER);

        $builderHeader
            ->addTitle('commission.show_title')
            ->addBackLink('app_commission_index')
            ->addButton('app_commission_edit', 'mdi:pencil' ,['guid' => $commission->getGuid()])
        ;

        return $builderHeader->build();
    }

    public function getHeaderRenderDataForGalleryShow(Commission $commission): array
    {
        $builderHeader = $this->componentFactory->create(ComponentFactory::CONTENT_HEADER);

        $builderHeader
            ->addTitle('commission.gallery_title')
            ->addButton('app_gallery_new', 'mdi:plus' ,['guid' => $commission->getGuid()])
        ;

        return $builderHeader->build();
    }

    public function getTableRenderDataForIndex(UserInterface $user): array
    {
        $commissions = $this->assigmentRepository->findAllForUser($user);

        $builderTable = $this->componentFactory->create(ComponentFactory::CONTENT_TABLE);

        $builderTable->addHeader('title', 'Title');
        $builderTable->addHeader('date', 'Date');

        foreach ($commissions as $commission) {
            $guid = $commission->getGuid();
            $builderTable->addRowValue($guid, 'guid', $guid);
            $builderTable->addRowValue($guid, 'title', $commission->getTitle());
            $builderTable->addRowValue($guid, 'date', $commission->getDate()->format('d.m.Y H:i:s'));
        }

        $builderTable->addAction('app_commission_show', ['guid' => 'guid'], 'mdi:eye');
        $builderTable->addAction('app_commission_edit', ['guid' => 'guid'], 'mdi:pencil');

        return $builderTable->build();
    }

    public function getTableRenderDataForGalleries(Commission $commission): array
    {
        $builderTable = $this->componentFactory->create(ComponentFactory::CONTENT_TABLE);

        $galleries = $commission->getGalleries();

        $builderTable->addHeader('name', 'Name');
        $builderTable->addHeader('public', 'public');

        foreach ($galleries as $gallery) {
            $guid = $gallery->getGuid();
            $builderTable->addRowValue($guid, 'guid', $guid);
            $builderTable->addRowValue($guid, 'name', $gallery->getName());
            $builderTable->addRowValue($guid, 'public', $gallery->isPublic() ? 'Yes' :  'No', $gallery->isPublic() ? 'green' :  'red');

        }

        $builderTable->addAction('app_gallery_show', ['guid' => 'guid'], 'mdi:eye');


        return $builderTable->build();
    }

}