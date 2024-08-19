<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Telemetry\ChatMessages\PerChatMember;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;
use App\Choco\Application\ChatCommandContracts\AllowedChatTypeEnum;

final class MyStatisticCommand
    extends AbstractChocoChatCommand
{
    public AllowedChatTypeEnum $allowedChatType = AllowedChatTypeEnum::CHAT;

    public static function getChatCommandPattern(): string
    {
        return '/^Кто\sя|Хто\sя/';
    }

    public static function getChatCommandExample(): string
    {
        return 'Кто я,Хто я';
    }

    public static function getChatCommandDescription(): string
    {
        return 'Получить детальную статистику о своей активности в чате.';
    }
}
