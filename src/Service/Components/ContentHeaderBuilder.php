<?php

namespace App\Service\Components;

use Symfony\Contracts\Translation\TranslatorInterface;

class ContentHeaderBuilder implements ComponentInterface
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

    public function addTitle(string $title, array $parameters = []): self
    {
        $this->result['title'] = $this->translator->trans($title, $parameters);

        return $this;
    }

    public function addBackLink(string $route, array $parameters = []): self
    {
        $this->result['back_link'] = [
                'route' => $route,
                'params' => $parameters,
            ];

        return $this;
    }

    public function addButton(string $route, string $icon, array $parameters = []): self
    {
        $this->result['buttons'][]= [
            'route' => $route,
            'params' => $parameters,
            'icon' => $icon,
            'class' => 'bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-md',
        ];

        return $this;
    }
}