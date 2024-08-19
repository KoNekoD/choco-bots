<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Help;

use App\Choco\Application\Service\ChocoChatCommandHandlerFactory;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Shared\Application\Command\CommandHandlerInterface;
use Twig\Environment;
use Twig\Error\Error;

final readonly class ChocoHelpCommandHandler
    implements CommandHandlerInterface
{
    public function __construct(
        private ChocoChatCommandHandlerFactory $chatCommandHandler,
        private Environment $environment
    ) {}

    /** @throws Error */
    public function __invoke(ChocoHelpCommand $command): ChatClientResultDTO
    {
        $commands = $this->chatCommandHandler->getList();
        $content = $this->environment->render(
            'messages/help.html.twig',
            ['commands' => $commands]
        );

        return $command->chocoData->client->sendMessage(
            $command->getSourceChatId(),
            $content
        );
    }
}
