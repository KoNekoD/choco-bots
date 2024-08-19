<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Moderation\RankAssignment;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;
use App\Choco\Application\ChatCommandContracts\AllowedChatTypeEnum;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;

final class SetRankCommand
    extends AbstractChocoChatCommand
{
    public AllowedChatTypeEnum $allowedChatType = AllowedChatTypeEnum::CHAT;

    /**
     * $args[0] - Full command
     * $args[1] - Rank level, counted by '!' symbol
     * $args[2] - Command body
     * $args[3] - Targeted chat user.
     */
    public static function getChatCommandPattern(): string
    {
        return '/^([!]+)(модер)\s(.*)$/';
    }

    public static function getChatCommandExample(): string
    {
        return '!!модер Иванов Иван';
    }

    public static function getChatCommandDescription(): string
    {
        return
            'Установить определённый ранг участнику. '.
            'Количество восклицательных знаков определяют уровень ранга ';
    }

    public function getNewRankValue(): int
    {
        $args = $this->getCommandArguments();

        return strlen($args[1]);
    }

    /** @throws ChatClientAPIException */
    public function getTargetUsername(): string
    {
        $args = $this->getCommandArguments();

        return $this->chocoData->client->trimUsername($args[3]);
    }
}
