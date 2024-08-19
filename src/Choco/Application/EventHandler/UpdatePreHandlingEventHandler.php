<?php

declare(strict_types=1);

namespace App\Choco\Application\EventHandler;

use App\Choco\Application\Service\ChatMemberManager;
use App\Choco\Domain\Service\ChocoChatClientApiProviderFactoryInterface;
use App\Main\Domain\ChatClientAPI\ChatClientInterface;
use App\Main\Domain\Entity\Update\Update;
use App\Main\Domain\Enum\UpdateChatTypeEnum;
use App\Main\Domain\Event\UpdatePreHandlingEvent;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;
use App\Shared\Domain\Event\EventHandlerInterface;
use Exception;
use Psr\Log\LoggerInterface;

final readonly class UpdatePreHandlingEventHandler
    implements EventHandlerInterface
{
    public function __construct(
        private ChatMemberManager $chatMemberManager,
        private LoggerInterface $logger,
        private ChocoChatClientApiProviderFactoryInterface $apiProviderFactory
    ) {}

    /** @throws ChatClientAPIException
     * @throws Exception
     */
    public function __invoke(UpdatePreHandlingEvent $event): void
    {
        $update = $event->getUpdate();

        $api = $this->apiProviderFactory->getApiByServiceName(
            $update->getSourceServiceName()
        );

        if (!$this->isBelongsToMe($update, $api)) {
            return;
        }

        $updateChat = $update->getChat();
        $updateMessage = $update->getMessage();

        if (null === $updateChat) {
            throw new Exception(
                'TryHandleUpdateStartedEventHandler: Update chat is null'
            );
        }

        if (null === $updateMessage) {
            throw new Exception(
                'TryHandleUpdateStartedEventHandler: Update message is null'
            );
        }

        // ChocoChat member information update section
        // Тут можно тоже прикреплять к сущности ChatMember Updates
        if (UpdateChatTypeEnum::GROUP === $updateChat->getType()) {
            try {
                if ($updateMessage->getFrom() !== null) {
                    $this->chatMemberManager->syncChatMemberInformation(
                        $update->getSourceServiceName(),
                        $updateChat->getSourceChatId(),
                        $updateMessage->getFrom()->getSourceUserId()
                    );
                }

                if ($updateMessage->getForwardFrom() !== null) {
                    $this->chatMemberManager->syncChatMemberInformation(
                        $update->getSourceServiceName(),
                        $updateChat->getSourceChatId(),
                        $updateMessage->getForwardFrom()->getSourceUserId()
                    );
                }

                if ($updateMessage->getNewChatMembers()) {
                    foreach ($updateMessage->getNewChatMembers(
                    ) as $newChatMember) {
                        $this->chatMemberManager->syncChatMemberInformation(
                            $update->getSourceServiceName(),
                            $updateChat->getSourceChatId(),
                            $newChatMember->getSourceUserId()
                        );
                    }
                }
                if ($updateMessage->getLeftChatMember() !== null) {
                    $this->chatMemberManager->syncChatMemberInformation(
                        $update->getSourceServiceName(),
                        $updateChat->getSourceChatId(),
                        $updateMessage->getLeftChatMember()->getSourceUserId()
                    );
                }
            } catch (ChatClientAPIException $e) {
                $this->logger->warning(
                    'Failed update ChatMember entity',
                    [
                        'error' => $e->getMessage(),
                        'updateId' => $update->getId(),
                    ]
                );
                $update->handleRejected();

                return;
            }

            $event->setUpdate($update);
            $event->stopPropagation();
        }
    }

    private function isBelongsToMe(
        Update $update,
        ChatClientInterface $api
    ): bool {
        if ($api::getBotId() === $update->getBotId()) {
            return true;
        }

        return false;
    }
}
