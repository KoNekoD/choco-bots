<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Settings\ChatConfiguration\NotifyAboutNewChatMemberInSystem;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;
use App\Choco\Application\ChatCommandContracts\AllowedChatTypeEnum;

final class EnableCommand
    extends AbstractChocoChatCommand
{
    public AllowedChatTypeEnum $allowedChatType = AllowedChatTypeEnum::CHAT;

    public static function getChatCommandPattern(): string
    {
        return '/\+(NotifyNewChatMembersInSystem|СообщатьОбНовыхУчастникахВСистеме)/';
    }

    public static function getChatCommandExample(): string
    {
        return '+СообщатьОбНовыхУчастникахВСистеме';
    }

    public static function getChatCommandDescription(): string
    {
        return 'Включает оповещение о новых участниках в чате о которых бот не знал ранее';
    }
}
