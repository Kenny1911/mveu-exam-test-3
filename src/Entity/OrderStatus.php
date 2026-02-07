<?php

declare(strict_types=1);

namespace App\Entity;

enum OrderStatus: string
{
    case NEW = 'new';
    case APPROVE = 'approve';
    case COMPLETE = 'complete';
    case CANCEL = 'cancel';

    public function title(): string
    {
        return match ($this) {
            self::NEW => 'Новая',
            self::APPROVE => 'Подтверждена',
            self::COMPLETE => 'Завершена',
            self::CANCEL => 'Отменена',
        };
    }
}
