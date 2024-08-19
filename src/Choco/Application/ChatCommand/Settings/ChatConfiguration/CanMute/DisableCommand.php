<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Settings\ChatConfiguration\CanMute;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;
use App\Choco\Application\ChatCommandContracts\AllowedChatTypeEnum;

final class DisableCommand
    extends AbstractChocoChatCommand
{
    public AllowedChatTypeEnum $allowedChatType = AllowedChatTypeEnum::CHAT;

    public static function getChatCommandPattern(): string
    {
        return '/\-(CanMute|Муты)/';
    }

    public static function getChatCommandExample(): string
    {
        return '-СообщатьОбНовыхУчастникахВСистеме';
    }

    public static function getChatCommandDescription(): string
    {
        return 'Отключает оповещение о новых участниках '.
            'в чате о которых бот не знал ранее';
    }
}
