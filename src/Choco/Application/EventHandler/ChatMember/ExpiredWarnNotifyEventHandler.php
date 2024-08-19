<?php

declare(strict_types=1);

namespace App\Choco\Application\EventHandler\ChatMember;

use App\Choco\Domain\Event\ChatMember\ExpiredWarnNotifyEvent;
use App\Choco\Domain\Exception\ChocoEntity\ChatMemberException;
use App\Choco\Domain\Repository\ChatMemberRepositoryInterface;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;
use App\Main\Domain\Service\ChatClientApiProviderFactoryInterface;
use App\Shared\Domain\Event\EventHandlerInterface;

final readonly class ExpiredWarnNotifyEventHandler
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
    public function __invoke(ExpiredWarnNotifyEvent $event): void
    {
        $chatMember = $this->chatMemberRepository->findChatMemberById(
            $event->chatMemberId
        );
        $chatMemberWarn = $chatMember->getWarnById($event->chatMemberWarnId);
        $api = $this->apiProviderFactory->getApiByServiceName(
            $chatMember->getSourceServiceName()
        );

        $api->sendMessage(
            $chatMember->getChat()->getSourceChatId(),
            sprintf(
                'Предупреждение, выданное %s админом %s, %s до %s по причине %s было снято автоматически',
                $chatMember->getUser()->getUpdateUser()->getFirstName(),
                $chatMemberWarn->getCreator()->getUser()->getUpdateUser(
                )->getFirstName(),
                $chatMemberWarn->getCreatedAt()->format('Y-m-d H:i:s'),
                $chatMemberWarn->getExpiresAt()->format('Y-m-d H:i:s'),
                $chatMemberWarn->getReason()
            )
        );
    }
}
