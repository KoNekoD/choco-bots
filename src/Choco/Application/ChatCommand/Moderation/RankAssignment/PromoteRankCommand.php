<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Moderation\RankAssignment;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;
use App\Choco\Application\ChatCommandContracts\AllowedChatTypeEnum;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;

final class PromoteRankCommand
    extends AbstractChocoChatCommand
{
    public AllowedChatTypeEnum $allowedChatType = AllowedChatTypeEnum::CHAT;

    /**
     * $args[0] - Full command
     * $args[1] - Command body
     * $args[2] - Empty(whitespace)
     * $args[3] - Rank level(int|null)
     * $args[4] - Targeted chat user.
     */
    public static function getChatCommandPattern(): string
    {
        return '/^(Повысить|повысить)(\s(\d+))?\s(.*)$/';
    }

    public static function getChatCommandExample(): string
    {
        return 'Повысить ?2 Иванов Иван';
    }

    public static function getChatCommandDescription(): string
    {
        return
            'Повысить участника на 1 или более рангов вверх. '.
            'Второй параметр необязательный, это количество рангов(на сколько повысить). '.
            'Без него пользователь будет повышен на 1 ранг';
    }

    public function getPromoteRanksValue(): int
    {
        $args = $this->getCommandArguments();
        if (isset($args[3]) && $args[3] !== '') {
            return (int)$args[3];
        }

        return 1;
    }

    /** @throws ChatClientAPIException */
    public function getTargetUsername(): string
    {
        $args = $this->getCommandArguments();

        return $this->chocoData->client->trimUsername($args[4]);
    }
}
