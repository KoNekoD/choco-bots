<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Reputation\Demote;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;
use App\Choco\Application\ChatCommandContracts\AllowedChatTypeEnum;

final class DemoteReputationCommand
    extends AbstractChocoChatCommand
{
    public AllowedChatTypeEnum $allowedChatType = AllowedChatTypeEnum::CHAT;
    public bool $requiredReplyToMessage = true;

    public static function getChatCommandPattern(): string
    {
        return '/^[-]+$/';
    }

    public static function getChatCommandExample(): string
    {
        return '(в ответ на сообщение)--';
    }

    public static function getChatCommandDescription(): string
    {
        return
            'Ответьте на сообщение со знаком - чтобы понизить репутацию на 1'.
            '(если нужно больше то можно поставить больше ---)';
    }

    public function getPromoteReputationValue(): int
    {
        $args = $this->getCommandArguments();

        return strlen($args[0]);
    }
}
