<?php

declare(strict_types=1);

namespace App\Main\Application\Service;

use App\Main\Application\ChatCommandContracts\AbstractChatCommand;
use App\Shared\Application\Command\CommandInterface;
use Psr\Log\LoggerInterface;
use Traversable;

abstract class AbstractChatCommandHandlerFactory
{
    /**
     * @var AbstractChatCommand[]
     */
    protected array $commands;

    /**
     * @param iterable<AbstractChatCommand> $commands
     */
    public function __construct(
        iterable $commands,
        protected readonly LoggerInterface $logger
    ) {
        $this->commands = $commands instanceof Traversable
            ? iterator_to_array($commands)
            : $commands;
    }

    /** @see AbstractChatCommand */
    public function tryDefineSuitableCommand(
        string $inputCommandMessage
    ): ?CommandInterface {
        foreach ($this->commands as $command) {
            $matches = [];
            if (
                preg_match(
                    $command::getChatCommandPattern(),
                    $inputCommandMessage,
                    $matches
                )
            ) {
                $this->logger->info(
                    'Command: '.$command::class,
                    ['command' => $matches]
                );
                $commandObj = new $command();
                $commandObj->setCommandArguments($matches);

                return $commandObj;
            }
        }

        return null;
    }

    /** @return AbstractChatCommand[] */
    public function getList(): array
    {
        return $this->commands;
    }
}
