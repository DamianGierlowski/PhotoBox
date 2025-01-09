<?php

namespace App\Service\Components;

interface ComponentInterface
{
    public function build(array $options = []): array;
}