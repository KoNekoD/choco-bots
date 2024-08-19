<?php

declare(strict_types=1);

namespace App\Choco\Application\EventHandler\Marry;

use App\Choco\Domain\Event\Marry\SuccessfullyMarriedEvent;
use App\Choco\Domain\Exception\ChocoEntity\UserNotFoundException;
use App\Choco\Domain\Repository\ChocoRepositoryInterface;
use App\Choco\Domain\Service\ChocoChatClientApiProviderFactoryInterface;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;
use App\Shared\Domain\Event\EventHandlerInterface;

final readonly class SuccessfullyMarriedEventHandler
    implements EventHandlerInterface
{
    public function __construct(
        private ChocoRepositoryInterface $chocoRepository,
        private ChocoChatClientApiProviderFactoryInterface $apiProviderFactory,
    ) {}

    /**
     * @throws UserNotFoundException
     * @throws ChatClientAPIException
     */
    public function __invoke(SuccessfullyMarriedEvent $event): void
    {
        $participantsToNotify = [];
        foreach ($event->participantIds as $participantId) {
            $participantsToNotify[] = $this->chocoRepository->findUserById(
                $participantId
            );
        }

        $client = $this->apiProviderFactory->getApiByServiceName(
            $participantsToNotify[0]->getSourceServiceName()
        );

        $mentionString = '';
        foreach ($participantsToNotify as $participant) {
            if ('' !== $mentionString) {
                $mentionString .= ', ';
            }
            $mentionString .= $client->getChatMemberMentionString(
                $participant->getUpdateUser()->getFirstName(),
                $participant->getUpdateUser()
            );
        }

        $client->sendMessage(
            $event->sourceChatId,
            sprintf(
                'Брак между %s успешно заключен',
                $mentionString
            )
        );
    }
}
