<?php

namespace App\Util;


use Symfony\Component\Uid\Uuid;

class GuidFactory
{
    static public function generate(): string
    {
        return strtoupper(Uuid::v6());
    }
}