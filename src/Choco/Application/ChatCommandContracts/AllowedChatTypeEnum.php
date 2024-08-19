<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommandContracts;

enum AllowedChatTypeEnum
{
    case ALL; // Allow all
    case PM; // Only private messages
    case CHAT; // Only group messages
}
