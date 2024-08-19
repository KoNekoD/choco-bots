<?php

declare(strict_types=1);

namespace App\Choco\Application\EventHandler;

use App\Choco\Application\ChatCommandDTO\ChocoCommandDataStructure;
use App\Choco\Application\Service\ChocoChatCommandHandlerFactory;
use App\Choco\Domain\Exception\ChocoEntity\ChatMemberException;
use App\Choco\Domain\Exception\ChocoEntity\ChocoChatNotFoundException;
use App\Choco\Domain\Exception\RequiredConfigurationException;
use App\Choco\Domain\Repository\ChatMemberRepositoryInterface;
use App\Choco\Domain\Repository\ChocoRepositoryInterface;
use App\Choco\Domain\Service\ChocoChatClientApiProviderFactoryInterface;
use App\Main\Application\Event\UpdateSearchCommandStartedEvent;
use App\Main\Domain\ChatClientAPI\ChatClientInterface;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;
use App\Shared\Domain\Event\EventHandlerInterface;
use Exception;

final readonly class UpdateSearchCommandStartedEventHandler
    implements EventHandlerInterface
{
    public function __construct(
        private ChocoChatCommandHandlerFactory $chatCommandHandler,
        private ChocoRepositoryInterface $chocoRepository,
        private ChocoChatClientApiProviderFactoryInterface $apiProvider,
        private ChatMemberRepositoryInterface $chatMemberRepository,
    ) {}

    /**
     * @throws ChatClientAPIException
     * @throws ChocoChatNotFoundException
     * @throws ChatMemberException
     * @throws Exception
     */
    public function __invoke(UpdateSearchCommandStartedEvent $event): void
    {
        $api = $this->apiProvider->getApiByServiceName(
            $event->getSourceServiceName()
        );

        if (!$this->isBelongsToMe($event->getBotId(), $api)) {
            return;
        }

        $command = $this->chatCommandHandler->tryDefineSuitableCommand(
            $event->getTextData()
        );

        if (null === $command) {
            return;
        }

        $who = $this->chatMemberRepository->findChatMember(
            $event->getSourceChatId(),
            $event->getSourceMessageFromSourceId(),
            $event->getSourceServiceName()
        );

        $chocoChat = $this->chocoRepository->getChocoChatByUpdateChat(
            $event->getUpdateChat()
        );

        $reply = $event->getUpdateMessageReplyToMessage();
        $replyFrom = $event->getUpdateMessageReplyToMessageFrom();

        $structure = new ChocoCommandDataStructure(
            chat: $chocoChat,
            client: $api,
            who: $who,
            replyFrom: $replyFrom,
            reply: $reply
        );

        $command->loadInitialChocoConfiguration($structure);

        if (!$command->checkIfNeededReplyToMessage()) {
            throw new Exception('checkIfNeededReplyToMessage');
        }

        try {
            $command->checkRequiredConfiguration();
        } catch (RequiredConfigurationException) {
            return; // Here we do not need to reply to chat
        }

        if (!$command->checkCommandChatTypeRule()) {
            throw new Exception('checkCommandChatTypeRule');
        }

        $event->setCommand($command);
        $event->stopPropagation();
    }

    private function isBelongsToMe(
        string $botId,
        ChatClientInterface $api
    ): bool {
        return $api::getBotId() === $botId;
    }
}
