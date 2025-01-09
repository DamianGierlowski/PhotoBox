<?php

namespace App\Service\Components;

use Symfony\Contracts\Translation\TranslatorInterface;

class ContentTableBuilder implements ComponentInterface
{

    private array $result = [];

    public function __construct(private TranslatorInterface $translator)
    {
    }

    #[\Override]
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

    public function addRowValue(string $identifier, string $key, mixed $value, string $color = ''): self
    {
        $this->result['rows'][$identifier][$key] = [
            'value' => $value,
            'color' => $color
        ];

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
}