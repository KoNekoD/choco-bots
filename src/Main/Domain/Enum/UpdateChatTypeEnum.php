<?php

declare(strict_types=1);

namespace App\Main\Domain\Enum;

enum UpdateChatTypeEnum: string
{
    case PRIVATE = 'private';
    case GROUP = 'group';
    case CHANNEL = 'channel';
}
