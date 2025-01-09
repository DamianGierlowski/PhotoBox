<?php

namespace App\Service\Gallery;

use App\Entity\Commission;
use App\Entity\Gallery;
use App\Service\Factory\ComponentFactory;

class GalleryRenderService
{

    public function __construct(private ComponentFactory $componentFactory)
    {

    }
    public function getHeaderRenderDataForNew(Commission $commission): array
    {
        $builderHeader = $this->componentFactory->create(ComponentFactory::CONTENT_HEADER);

        $builderHeader
            ->addTitle('gallery.new_title')
            ->addBackLink('app_commission_show', ['guid' => $commission->getGuid()])
        ;

        return $builderHeader->build();
    }

    public function getHeaderRenderDataForEdit(Commission $commission): array
    {
        $builderHeader = $this->componentFactory->create(ComponentFactory::CONTENT_HEADER);

        $builderHeader
            ->addTitle('gallery.edit_title')
            ->addBackLink('app_commission_show', ['guid' => $commission->getGuid()])
        ;

        return $builderHeader->build();
    }

    public function getHeaderRenderDataForShow(Gallery $gallery): array
    {
        $builderHeader = $this->componentFactory->create(ComponentFactory::CONTENT_HEADER);

        $builderHeader
            ->addTitle('gallery.show_title', ['%title%' => $gallery->getName()])
            ->addBackLink('app_commission_show', ['guid' => $gallery->getCommission()->getGuid()])
            ->addButton('app_gallery_edit', 'mdi:pencil', ['guid'=> $gallery->getGuid()])
            ->addButton('app_gallery_upload', 'mdi:tray-upload', ['guid'=> $gallery->getGuid()])
        ;

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