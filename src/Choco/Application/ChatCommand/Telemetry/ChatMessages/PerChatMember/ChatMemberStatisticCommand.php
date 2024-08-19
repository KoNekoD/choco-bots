<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Telemetry\ChatMessages\PerChatMember;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;
use App\Choco\Application\ChatCommandContracts\AllowedChatTypeEnum;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;

final class ChatMemberStatisticCommand
    extends AbstractChocoChatCommand
{
    public AllowedChatTypeEnum $allowedChatType = AllowedChatTypeEnum::CHAT;

    /**
     * (?!ABC) - Negative lookahead.
     * Specifies a group that can not match after the main expression
     * (if it matches, the result is discarded).
     */
    public static function getChatCommandPattern(): string
    {
        return '/^(Кто|Хто)\s((?!я).*)$/';
    }

    public static function getChatCommandExample(): string
    {
        return 'Кто @username, Хто @username';
    }

    public static function getChatCommandDescription(): string
    {
        return 'Получить детальную статистику '.
            'о активности другого участника чата.';
    }

    /** @throws ChatClientAPIException */
    public function getTargetUsername(): string
    {
        $args = $this->getCommandArguments();

        return $this->chocoData->client->trimUsername($args[2]);
    }
}
