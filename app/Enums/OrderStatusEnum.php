<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case Pending = 'pending';
    case Fulfilled = 'fulfilled';
    case Canceled = 'canceled';
}
