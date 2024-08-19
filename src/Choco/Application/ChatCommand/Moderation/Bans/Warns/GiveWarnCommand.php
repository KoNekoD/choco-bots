<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Moderation\Bans\Warns;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;
use App\Choco\Application\ChatCommandContracts\AllowedChatTypeEnum;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;
use DateTimeImmutable;
use DomainException;

final class GiveWarnCommand
    extends AbstractChocoChatCommand
{
    public AllowedChatTypeEnum $allowedChatType = AllowedChatTypeEnum::CHAT;

    /**
     * Варн на 20 дней @user Флуд:
     * [1]=> string(8) "Варн"
     * [2]=> string(21) "Иванов Иван"
     * [3]=> string(50) "Пользователь совершал спам"
     * [4]=> string(14) "на 22 дня"
     * [5]=> string(9) "22 дня"
     * [6]=> string(3) "22 "
     * [7]=> string(2) "22"
     * [8]=> string(6) "дня".
     */
    public static function getChatCommandPattern(): string
    {
        return '/^(Варн)\s(.*)\n(.*)\n(на\s(((\d+)\s)?(день|дня|дней)))$/';
    }

    public static function getChatCommandExample(): string
    {
        return
            "Варн Иванов Иван\n".
            "Пользователь совершал спам\n".
            'на 22 дня';
    }

    public static function getChatCommandDescription(): string
    {
        return 'Выдать предупреждение пользователю. На второй строке причина предупреждения. На третьей срок(пока что только в днях)';
    }

    public function getWarnReason(): string
    {
        $args = $this->getCommandArguments();
        if (isset($args[3])) {
            return $args[3];
        }

        throw new DomainException('Вы забыли указать причину');
    }

    /** @throws ChatClientAPIException */
    public function getTargetUsername(): string
    {
        return $this->chocoData->client->trimUsername(
            $this->getCommandArguments()[2]
        );
    }

    public function getWarnExpireDateTime(): DateTimeImmutable
    {
        $days = $this->getCommandArguments()[7];

        return (new DateTimeImmutable())->modify("+$days days");
    }
}
