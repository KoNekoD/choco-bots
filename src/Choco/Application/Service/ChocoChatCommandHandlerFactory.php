<?php

declare(strict_types=1);

namespace App\Choco\Application\Service;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;
use App\Main\Application\Service\AbstractChatCommandHandlerFactory;

/**
 * Used for searching suitable command in choco domain(See services.yaml).
 */
final class ChocoChatCommandHandlerFactory
    extends AbstractChatCommandHandlerFactory
{
    public function tryDefineSuitableCommand(
        string $inputCommandMessage
    ): ?AbstractChocoChatCommand {
        $command = parent::tryDefineSuitableCommand(
            $inputCommandMessage
        );

        if ($command instanceof AbstractChocoChatCommand) {
            return $command;
        }

        return null;
    }
}
