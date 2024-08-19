<?php

declare(strict_types=1);

namespace App\Main\Infrastructure\DTO;

use App\Main\Application\ChatCommandContracts\AbstractChatCommand;

final readonly class ChatCommandViewItemDTO
{
    /**
     * @param string[] $commandBreadcrumbs
     */
    public function __construct(
        public string $pattern,
        public string $example,
        public string $description,
        public array $commandBreadcrumbs,
    ) {}

    public static function fromAbstractCommand(
        AbstractChatCommand $command
    ): self {
        $classPathArr = explode('\\', $command::class);

        $breadcrumbs = [];
        foreach ($classPathArr as $item) {
            if (
                in_array(
                    $item,
                    ['App', 'Application', 'ChatCommand']
                )
            ) {
                continue;
            }

            $breadcrumbs[] = $item;
        }

        return new self(
            $command::getChatCommandPattern(),
            $command::getChatCommandExample(),
            $command::getChatCommandDescription(),
            $breadcrumbs
        );
    }
}
