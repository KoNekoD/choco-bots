<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Fun\RolePlay;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;
use App\Choco\Application\ChatCommandContracts\AllowedChatTypeEnum;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;

final class SimpleRolePlayCommand
    extends AbstractChocoChatCommand
{
    public AllowedChatTypeEnum $allowedChatType = AllowedChatTypeEnum::CHAT;

    public static function getChatCommandPattern(): string
    {
        return '/^(\/me)\s(\S+)\s(.*)((\s+)?\n.*)?$/';
    }

    public static function getChatCommandExample(): string
    {
        return
            "/me Обнял @username\n".
            'Люблю тебя~';
    }

    public static function getChatCommandDescription(): string
    {
        return 'Простой Role Play';
    }

    public function getRolePlayAction(): string
    {
        $args = $this->getCommandArguments();

        return $args[2];
    }

    /** @throws ChatClientAPIException */
    public function getRolePlayTarget(): string
    {
        $args = $this->getCommandArguments();

        return $this->chocoData->client->trimUsername($args[3]);
    }

    public function getRolePlayAdditionalMessage(): string
    {
        $args = $this->getCommandArguments();

        if (empty($args[4])) {
            return '';
        }

        return $args[4];
    }
}
