<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Moderation\GettingInformation;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;
use App\Choco\Application\ChatCommandContracts\AllowedChatTypeEnum;

final class WhoAdminCommand
    extends AbstractChocoChatCommand
{
    public AllowedChatTypeEnum $allowedChatType = AllowedChatTypeEnum::CHAT;

    public static function getChatCommandPattern(): string
    {
        return '/^Кто\sадмин$/';
    }

    public static function getChatCommandExample(): string
    {
        return 'Кто админ';
    }

    public static function getChatCommandDescription(): string
    {
        return 'Получить список пользователей. Кто имеет ранг выше участника.';
    }
}
