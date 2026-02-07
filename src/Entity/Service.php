<?php

declare(strict_types=1);

namespace App\Entity;

enum Service: string
{
    case COMMON = 'common';
    case GENERAL = 'general';
    case POST_CONSTRUCTION = 'post_construction';
    case DRY = 'dry';

    public function title(): string
    {
        return match ($this) {
            self::COMMON => 'Общий клининг',
            self::GENERAL => 'Генеральная уборка',
            self::POST_CONSTRUCTION => 'Послестроительная уборка',
            self::DRY => 'Химчистка ковров и мебели',
        };
    }
}
