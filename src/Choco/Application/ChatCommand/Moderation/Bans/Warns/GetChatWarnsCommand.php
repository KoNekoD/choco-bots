<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Moderation\Bans\Warns;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;
use App\Choco\Application\ChatCommandContracts\AllowedChatTypeEnum;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;

final class GetChatWarnsCommand
    extends AbstractChocoChatCommand
{
    public AllowedChatTypeEnum $allowedChatType = AllowedChatTypeEnum::CHAT;

    public static function getChatCommandPattern(): string
    {
        return '/^Варны(\s(.*))?$/';
    }

    public static function getChatCommandExample(): string
    {
        return 'Варны ?Иванов Иван';
    }

    public static function getChatCommandDescription(): string
    {
        return
            'Получить список предупреждений. '.
            'Если указать пользователя то получить подробно с причиной его предупреждения';
    }

    /** @throws ChatClientAPIException */
    public function getTargetUsername(): ?string
    {
        $args = $this->getCommandArguments();

        if (!isset($args[2])) {
            return null;
        }

        return $this->chocoData->client->trimUsername($args[2]);
    }
}
