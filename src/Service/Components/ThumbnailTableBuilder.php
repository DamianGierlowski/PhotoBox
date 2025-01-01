<?php

namespace App\Service\Components;

use Override;
use Symfony\Contracts\Translation\TranslatorInterface;

class ThumbnailTableBuilder implements ComponentInterface
{

    private array $result = [];

    public function __construct(private TranslatorInterface $translator)
    {
        $this->result['rows'] = [];
    }

    #[Override]
    public function build(array $options = []): array
    {
       return $this->result;
    }

    public function addHeader(string $key, string $label): self
    {

        $this->result['headers'][] = [
            'key' => $key,
            'label' => $this->translator->trans($label)
        ];

        return $this;
    }

    public function addRowValue(string $identifier, string $key, mixed $value): self
    {
        $this->result['rows'][$identifier][$key] = $value;

        return $this;
    }

    public function addAction(string $route, array $parameters, string $icon, string $class = ''): self
    {
        $this->result['actions'][] = [
            'route' => $route,
            'params' => $parameters,
            'icon' => $icon,
            'class' => $class,
        ];

        return $this;
    }

    public function addThumbnailConfig(string $imageUrlKey, string $thumbnailUrlKey, string $imageAltKey): self
    {
        $this->result['thumbnail_config']['image_url_key'] = $imageUrlKey;
        $this->result['thumbnail_config']['thumbnail_url_key'] = $thumbnailUrlKey;
        $this->result['thumbnail_config']['image_alt_key'] = $imageAltKey;

        return $this;
    }
}