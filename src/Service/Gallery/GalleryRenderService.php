<?php

namespace App\Service\Gallery;

use App\Entity\Commission;
use App\Entity\Gallery;
use App\Repository\CommissionRepository;
use App\Repository\GalleryRepository;
use App\Service\Factory\ComponentFactory;
use Symfony\Component\Security\Core\User\UserInterface;

class GalleryRenderService
{

    public function __construct(
        private ComponentFactory $componentFactory,
        private GalleryRepository $galleryRepository,
    ) {
    }


    public function getHeaderRenderDataForIndex(): array
    {
        $builderHeader = $this->componentFactory->create(ComponentFactory::CONTENT_HEADER);

        $builderHeader
            ->addTitle('gallery.index')
            ->addButton('app_gallery_new', 'mono-icons:add')
        ;

        return $builderHeader->build();
    }

    public function getTableRenderDataForIndex(UserInterface $user): array
    {

        $galleries = $this->galleryRepository->findBy(['commission' => null, 'createdBy' => $user]);

        /* ThumbnailTableBuilder $builder */
        $builder = $this->componentFactory->create(ComponentFactory::CONTENT_TABLE);

        $builder->addHeader('name', 'Name');
        $builder->addHeader('count', 'File count');
        $builder->addHeader('public', 'public');

        foreach ($galleries as $gallery) {
            $guid = $gallery->getGuid();

            $builder->addRowValue($guid, 'guid', $guid);
            $builder->addRowValue($guid, 'name', $gallery->getName());
            $builder->addRowValue($guid, 'count', count($gallery->getFiles()));
            $builder->addRowValue($guid, 'public', $gallery->isPublic() ? 'Yes' :  'No', $gallery->isPublic() ? 'green' :  'red');
        }

        $builder->addAction('app_gallery_show', ['guid' => 'guid'], 'mdi:eye');

        return $builder->build();
    }

    public function getHeaderRenderDataForNew(?string $guid): array
    {
        $builderHeader = $this->componentFactory->create(ComponentFactory::CONTENT_HEADER);

        $builderHeader
            ->addTitle('gallery.new_title')
        ;

        if (!empty($guid)) {
            $builderHeader->addBackLink('app_commission_show', ['guid' => $guid]);
        }

        return $builderHeader->build();
    }

    public function getHeaderRenderDataForEdit(?Gallery $gallery): array
    {
        $builderHeader = $this->componentFactory->create(ComponentFactory::CONTENT_HEADER);

        $builderHeader
            ->addTitle('gallery.edit_title')
        ;

        if (null === $gallery->getCommission()) {
            $builderHeader->addBackLink('app_gallery_index');
        } else {
            $builderHeader->addBackLink('app_commission_show', ['guid' => $gallery->getCommission()->getGuid()]);
        }

        return $builderHeader->build();
    }

    public function getHeaderRenderDataForShow(Gallery $gallery): array
    {
        $builderHeader = $this->componentFactory->create(ComponentFactory::CONTENT_HEADER);

        $builderHeader
            ->addTitle('gallery.show_title', ['%title%' => $gallery->getName()])
            ->addButton('app_gallery_edit', 'mdi:pencil', ['guid'=> $gallery->getGuid()])
            ->addButton('app_gallery_upload', 'mdi:tray-upload', ['guid'=> $gallery->getGuid()])
        ;

        if (null === $gallery->getCommission()) {
            $builderHeader->addBackLink('app_gallery_index');
        } else {
            $builderHeader->addBackLink('app_commission_show', ['guid' => $gallery->getCommission()->getGuid()]);
        }

        return $builderHeader->build();
    }

    public function getHeaderRenderDataForUpload(Gallery $gallery): array
    {
        $builderHeader = $this->componentFactory->create(ComponentFactory::CONTENT_HEADER);

        $builderHeader
            ->addTitle('gallery.upload_title', ['%title%' => $gallery->getName()])
            ->addBackLink('app_gallery_show', ['guid' => $gallery->getGuid()])
        ;

        return $builderHeader->build();
    }

    public function getTableRenderDataForShow(Gallery $gallery): array
    {
        /* ThumbnailTableBuilder $builder */
        $builder = $this->componentFactory->create(ComponentFactory::THUMBNAIL_TABLE);

        $builder->addHeader('thumbnail', 'Image')->addHeader('name', 'Name')->addThumbnailConfig('image_url','thumbnail_url','name');

        $files = $gallery->getFiles();

        foreach ($files as $file) {
            $guid = $file->getGuid();

            $builder->addRowValue($guid, 'guid_file', $guid);
            $builder->addRowValue($guid, 'gallery_guid', $gallery->getGuid());
            $builder->addRowValue($guid, 'name', $file->getName());
            $builder->addRowValue($guid, 'image_url', $file->getPath());
            $builder->addRowValue($guid, 'thumbnail_url', $file->getThumbnailPath());
        }

        $builder->addAction('app_gallery_file_delete', ['gallery_guid' => 'gallery_guid', 'guid_file' => 'guid_file'], 'mdi:trash');

        return $builder->build();
    }
}