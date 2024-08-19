<?php

declare(strict_types=1);

namespace App\Main\Application\ChatCommandContracts;

use App\Main\Application\ChatCommandDTO\CommandDataStructure;
use App\Shared\Application\Command\CommandInterface;

abstract class AbstractChatCommand
    implements CommandInterface
{
    public CommandDataStructure $data;

    /** @var array<string> */
    private array $commandArguments = [];

    /**
     * @see https://htmlweb.ru/php/php_regexp.php Regex guide
     */
    abstract public static function getChatCommandPattern(): string;

    abstract public static function getChatCommandExample(): string;

    abstract public static function getChatCommandDescription(): string;

    public function loadInitialConfiguration(
        CommandDataStructure $dataStructure
    ): void {
        $this->data = $dataStructure;
    }

    public function getSourceChatId(): int
    {
        return $this->data->getUpdateChatSourceChatId();
    }

    /** @return array<string> */
    protected function getCommandArguments(): array
    {
        return $this->commandArguments;
    }

    /** @param array<string> $commandArguments */
    public function setCommandArguments(array $commandArguments): void
    {
        $this->commandArguments = $commandArguments;
    }
}
