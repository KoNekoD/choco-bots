<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Moderation\Bans;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;
use App\Choco\Application\ChatCommandContracts\AllowedChatTypeEnum;
use App\Choco\Domain\DTO\RequiredChatConfigurationDTO;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;

final class MuteCommand
    extends AbstractChocoChatCommand
{
    public AllowedChatTypeEnum $allowedChatType = AllowedChatTypeEnum::CHAT;

    public static function getChatCommandPattern(): string
    {
        return '/^(Мут|мут)\s(.*)(\n(.*))?$/';
    }

    public static function getChatCommandExample(): string
    {
        return "Мут Иванов Иван\n?Пользователь совершал спам";
    }

    public static function getChatCommandDescription(): string
    {
        return 'Лишить права писать сообщения в чат пользователя на стандартный период чата. На второй строке причина ограничения';
    }

    public function getMuteReason(): ?string
    {
        $args = $this->getCommandArguments();
        if (isset($args[4]) && $args[4] !== '') {
            return $args[4];
        }

        return null;
    }

    /** @throws ChatClientAPIException */
    public function getTargetUsername(): string
    {
        $args = $this->getCommandArguments();

        return $this->chocoData->client->trimUsername($args[2]);
    }

    protected function getRequiredChatConfiguration(
    ): ?RequiredChatConfigurationDTO
    {
        return RequiredChatConfigurationDTO::muteRequired();
    }
}
