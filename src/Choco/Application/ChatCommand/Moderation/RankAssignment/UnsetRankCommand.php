<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Moderation\RankAssignment;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;
use App\Choco\Application\ChatCommandContracts\AllowedChatTypeEnum;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;

final class UnsetRankCommand
    extends AbstractChocoChatCommand
{
    public AllowedChatTypeEnum $allowedChatType = AllowedChatTypeEnum::CHAT;

    /**
     * $args[0] - Full command
     * $args[1] - Command body
     * $args[2] - Targeted chat user.
     */
    public static function getChatCommandPattern(): string
    {
        return '/^(Снять|Разжаловать)\s(.*)$/';
    }

    public static function getChatCommandExample(): string
    {
        return 'Разжаловать Иванов Иван';
    }

    public static function getChatCommandDescription(): string
    {
        return 'Понизить до уровня "Участник". Т.е снять с него все полномочия';
    }

    /** @throws ChatClientAPIException */
    public function getTargetUsername(): string
    {
        $args = $this->getCommandArguments();

        return $this->chocoData->client->trimUsername($args[2]);
    }
}
