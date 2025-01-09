<?php

namespace App\Twig;

use App\Util\ArchiveClient;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ArchiveExtension extends AbstractExtension
{
    public function __construct(
        private readonly ArchiveClient $archiveClient,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('archive_public_url', [$this, 'getPublicUrl']),
        ];
    }

    public function getPublicUrl(string $value): string
    {
        return $this->archiveClient->getFileUrl($value);
    }

}