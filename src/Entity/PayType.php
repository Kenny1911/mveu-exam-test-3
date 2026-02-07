<?php

declare(strict_types=1);

namespace App\Entity;

enum PayType: string
{
    case CASH = 'cash';
    case CARD = 'card';

    public function title(): string
    {
        return match ($this) {
            self::CASH => 'Наличные',
            self::CARD => 'Банковская карта',
        };
    }
}
