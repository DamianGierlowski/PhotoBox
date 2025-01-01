<?php

namespace App\Service\Factory;

use App\Service\Components\ComponentInterface;
use App\Service\Components\ContentHeaderBuilder;
use App\Service\Components\ContentTableBuilder;
use App\Service\Components\ThumbnailTableBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;

class ComponentFactory
{

    public const CONTENT_HEADER = 'content_header';
    public const CONTENT_TABLE = 'content_table';
    public const THUMBNAIL_TABLE = 'thumbnail_table';

    public function __construct(
        private TranslatorInterface $translator,
    ) {

    }

    public function create(string $type): ComponentInterface
    {
        return match ($type) {
            self::CONTENT_HEADER =>  new ContentHeaderBuilder($this->translator),
            self::CONTENT_TABLE =>  new ContentTableBuilder($this->translator),
            self::THUMBNAIL_TABLE =>  new ThumbnailTableBuilder($this->translator),
            default => throw new \InvalidArgumentException("Unknown builder type: $type"),
        };
    }
}