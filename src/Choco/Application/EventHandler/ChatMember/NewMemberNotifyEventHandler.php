<?php

declare(strict_types=1);

namespace App\Choco\Application\EventHandler\ChatMember;

use App\Choco\Domain\Event\ChatMember\NewMemberNotifyEvent;
use App\Choco\Domain\Exception\ChocoEntity\ChatMemberException;
use App\Choco\Domain\Repository\ChatMemberRepositoryInterface;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;
use App\Main\Domain\Service\ChatClientApiProviderFactoryInterface;
use App\Shared\Domain\Event\EventHandlerInterface;

final readonly class NewMemberNotifyEventHandler
    implements EventHandlerInterface
{
    public function __construct(
        private ChatMemberRepositoryInterface $chatMemberRepository,
        private ChatClientApiProviderFactoryInterface $apiProviderFactory,
    ) {}

    /**
     * @throws ChatMemberException
     * @throws ChatClientAPIException
     */
    public function __invoke(NewMemberNotifyEvent $event): void
    {
        $chatMember = $this->chatMemberRepository->findChatMemberById(
            $event->chatMemberId
        );

        $api = $this->apiProviderFactory->getApiByServiceName(
            $chatMember->getSourceServiceName()
        );

        $api->sendMessage(
            $chatMember->getChat()->getSourceChatId(),
            sprintf(
                'Участник %s добавлен в систему, в чате %s',
                $chatMember->getUser()->getUpdateUser()->getFirstName(),
                $chatMember->getChat()->getUpdateChat()->getTitle()
            )
        );
    }
}
