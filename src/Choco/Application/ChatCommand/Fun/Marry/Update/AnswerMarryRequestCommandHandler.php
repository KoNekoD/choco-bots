<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Fun\Marry\Update;

use App\Choco\Domain\Exception\Marry\MarryException;
use App\Choco\Domain\Repository\MarryRepositoryInterface;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Shared\Application\Command\CommandHandlerInterface;

final readonly class AnswerMarryRequestCommandHandler
    implements CommandHandlerInterface
{
    public function __construct(
        private MarryRepositoryInterface $marryRepository,
    ) {}

    public function __invoke(
        AnswerMarryRequestCommand $command
    ): ChatClientResultDTO {
        $who = $command->chocoData->who;

        try {
            if ($who->getUser()->isMarryParticipantStatusAccepted()) {
                throw new MarryException(
                    'Вы уже приняли предложение. Развестись: !Развод'
                );
            }

            if ($command->isAnswerAccept() && !$who->getUser()->isMarried()) {
                $who->getUser()->acceptMarry(
                    $command->getSourceChatId()
                );
                $command->chocoData->client->sendMessage(
                    $command->getSourceChatId(),
                    sprintf(
                        'Брак принят %s',
                        $who->getUserFirstName()
                    )
                );
            } else {
                $marry = $who->getUser()->getMarry();
                if ($marry && !$who->getUser()->isMarried()) {
                    $marry->divorce();
                    $this->marryRepository->remove($marry, true);
                    $command->chocoData->client->sendMessage(
                        $command->getSourceChatId(),
                        sprintf(
                            'Брак отвергнут %s',
                            $who->getUserFirstName()
                        )
                    );
                }
            }

            return ChatClientResultDTO::success();
        } catch (MarryException $e) {
            $command->chocoData->client->sendMessage(
                $command->getSourceChatId(),
                $e->getMessage()
            );

            return ChatClientResultDTO::fatal($e);
        }
    }
}
