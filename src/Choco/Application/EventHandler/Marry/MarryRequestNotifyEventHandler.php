<?php

declare(strict_types=1);

namespace App\Choco\Application\EventHandler\Marry;

use App\Choco\Domain\Event\Marry\MarryRequestEvent;
use App\Choco\Domain\Exception\ChocoEntity\ChatMemberException;
use App\Choco\Domain\Exception\ChocoEntity\UserNotFoundException;
use App\Choco\Domain\Repository\ChatMemberRepositoryInterface;
use App\Choco\Domain\Repository\ChocoRepositoryInterface;
use App\Choco\Domain\Service\ChocoChatClientApiProviderFactoryInterface;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;
use App\Shared\Domain\Event\EventHandlerInterface;

final readonly class MarryRequestNotifyEventHandler
    implements EventHandlerInterface
{
    public function __construct(
        private ChocoRepositoryInterface $chocoRepository,
        private ChocoChatClientApiProviderFactoryInterface $apiProviderFactory,
        private ChatMemberRepositoryInterface $chatMemberRepository,
    ) {}

    /**
     * @throws UserNotFoundException
     * @throws ChatClientAPIException
     * @throws ChatMemberException
     */
    public function __invoke(MarryRequestEvent $event): void
    {
        $creator = $this->chatMemberRepository->findChatMemberById(
            $event->creatorChatMemberId
        );
        $creatorUserId = $creator->getUser()->getId();

        $participantsToNotify = [];
        foreach ($event->participantUserIds as $participantUserId) {
            if ($participantUserId !== $creatorUserId) {
                $participantsToNotify[] = $this->chocoRepository->findUserById(
                    $participantUserId
                );
            }
        }

        if ($participantsToNotify === []) {
            $client = $this->apiProviderFactory->getApiByServiceName(
                $creator->getUser()->getSourceServiceName()
            );
            $client->sendMessage(
                $creator->getChat()->getSourceChatId(),
                sprintf(
                    'Участник %s сделал предложение... Но участников нет',
                    $creator->getUser()->getUpdateUser()->getFirstName(),
                )
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
            $creator->getChat()->getSourceChatId(),
            sprintf(
                'Участник %s сделал предложение %s. Принять предложение: Брак да, Отвергнуть: Брак нет',
                $creator->getUser()->getUpdateUser()->getFirstName(),
                $mentionString
            )
        );
    }
}
