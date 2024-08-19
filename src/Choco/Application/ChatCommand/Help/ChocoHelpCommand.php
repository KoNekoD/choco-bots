<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Help;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;

final class ChocoHelpCommand
    extends AbstractChocoChatCommand
{
    public static function getChatCommandPattern(): string
    {
        return '/^Помощь$/';
    }

    public static function getChatCommandExample(): string
    {
        return 'Помощь';
    }

    public static function getChatCommandDescription(): string
    {
        return 'Показывает список доступных команд';
    }
}
