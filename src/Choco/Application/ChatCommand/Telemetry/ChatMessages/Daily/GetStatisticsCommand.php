<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Telemetry\ChatMessages\Daily;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;
use App\Choco\Application\ChatCommandContracts\AllowedChatTypeEnum;

final class GetStatisticsCommand
    extends AbstractChocoChatCommand
{
    public AllowedChatTypeEnum $allowedChatType = AllowedChatTypeEnum::CHAT;

    public static function getChatCommandPattern(): string
    {
        return '/^Стата$/';
    }

    public static function getChatCommandExample(): string
    {
        return 'Стата';
    }

    public static function getChatCommandDescription(): string
    {
        return 'Получить статистику за сутки по участникам написавшим большее количество сообщений';
    }
}
