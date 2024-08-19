<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Fun\Marry\Delete;

use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Shared\Application\Command\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DivorceMarryCommandHandler
    implements CommandHandlerInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function __invoke(DivorceMarryCommand $command): ChatClientResultDTO
    {
        $who = $command->chocoData->who;

        $marry = $who->getUser()->getMarry();

        if ($marry !== null) {
            $text = 'Брак между '.
                $marry->getFullParticipantsFirstNamesString(', ').
                ' расторгнут.';

            $marry->divorce();
            $this->em->remove($marry);

            $command->chocoData->client->sendMessage(
                $command->getSourceChatId(),
                $text
            );
        }

        return ChatClientResultDTO::success();
    }
}
