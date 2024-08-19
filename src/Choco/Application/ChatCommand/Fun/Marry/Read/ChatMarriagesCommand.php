<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Fun\Marry\Read;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;
use App\Choco\Application\ChatCommandContracts\AllowedChatTypeEnum;

final class ChatMarriagesCommand
    extends AbstractChocoChatCommand
{
    public AllowedChatTypeEnum $allowedChatType = AllowedChatTypeEnum::CHAT;

    public static function getChatCommandPattern(): string
    {
        return '/Браки/';
    }

    public static function getChatCommandExample(): string
    {
        return 'Браки';
    }

    public static function getChatCommandDescription(): string
    {
        return 'Откроет список всех браков в чате';
    }
}
