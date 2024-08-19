<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Fun\RolePlay;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;
use App\Choco\Application\ChatCommandContracts\AllowedChatTypeEnum;

final class ReplyRolePlayCommand
    extends AbstractChocoChatCommand
{
    public AllowedChatTypeEnum $allowedChatType = AllowedChatTypeEnum::CHAT;
    public bool $requiredReplyToMessage = true;

    public static function getChatCommandPattern(): string
    {
        return '/^(\/do)\s(.*)((\s+)?\n.*)?$/';
    }

    public static function getChatCommandExample(): string
    {
        return
            "(В ответ на сообщение)/do Обнял\n".
            'Люблю тебя~';
    }

    public static function getChatCommandDescription(): string
    {
        return 'Role Play через ответ';
    }

    public function getRolePlayAction(): string
    {
        $args = $this->getCommandArguments();

        return $args[2];
    }

    public function getRolePlayAdditionalMessage(): string
    {
        $args = $this->getCommandArguments();

        if (empty($args[3])) {
            return '';
        }

        return $args[3];
    }
}
