<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Fun\Marry\Delete;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;

final class DivorceMarryCommand
    extends AbstractChocoChatCommand
{
    public static function getChatCommandPattern(): string
    {
        return '/\!Развод/';
    }

    public static function getChatCommandExample(): string
    {
        return '!Развод';
    }

    public static function getChatCommandDescription(): string
    {
        return 'Расторгнуть брака';
    }
}
