<?php

declare(strict_types=1);

namespace App\Choco\Domain\Enum;

enum MarryStatusEnum: string
{
    case MARRY_REQUEST = 'Marry request';
    case MARRIED = 'Married';
    case DIVORCE = 'Divorce';
}
