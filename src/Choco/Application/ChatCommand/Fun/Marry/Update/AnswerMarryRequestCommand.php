<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Fun\Marry\Update;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;

final class AnswerMarryRequestCommand
    extends AbstractChocoChatCommand
{
    public static function getChatCommandPattern(): string
    {
        return '/^(Брак)\s(да|нет)$/';
    }

    public static function getChatCommandExample(): string
    {
        return 'Брак да, Брак нет';
    }

    public static function getChatCommandDescription(): string
    {
        return 'принять/отвергнуть предложение на создание брака';
    }

    public function isAnswerAccept(): bool
    {
        $args = $this->getCommandArguments();

        return 'да' === $args[2];
    }
}
