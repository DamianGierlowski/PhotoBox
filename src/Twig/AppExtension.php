<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_cell', [$this, 'renderCell'], ['is_safe' => ['html']]),
            new TwigFunction('get_nested_value', [$this, 'getNestedValue']),
        ];
    }

    public function renderCell($cell): string
    {
        $colorClass = 'text-gray-500';
        $value = $cell;

        if (is_array($cell) && isset($cell['value'])) {
            $value = $cell['value'];
            if (isset($cell['color'])) {
                $colorClass = match ($cell['color']) {
                    'green' => 'text-green-600',
                    'red' => 'text-red-600',
                    'yellow' => 'text-yellow-600',
                    'blue' => 'text-blue-600',
                    default => 'text-gray-500',
                };
            }
        }

        return sprintf('<span class="%s">%s</span>', $colorClass, htmlspecialchars($value));
    }

    public function getNestedValue(array $array, string $path)
    {
        $keys = explode('.', $path);
        $value = $array;

        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                return null;
            }

            $value = $value[$key];
        }

        return is_array($value) ? $value['value'] : $value;
    }
}

