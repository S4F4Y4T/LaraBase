<?php

namespace App\Enums\V1;

enum PaymentStatus: string
{
    case DUE = 'due';
    case PAID = 'paid';
}
