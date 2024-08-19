<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Reputation\Demote;

use App\Choco\Domain\Exception\ChocoEntity\ChatMemberException;
use App\Choco\Domain\Exception\ChocoEntity\ChatMemberReputationException;
use App\Choco\Domain\Repository\ChatMemberRepositoryInterface;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Shared\Application\Command\CommandHandlerInterface;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DemoteReputationCommandHandler
    implements CommandHandlerInterface
{
    public function __construct(
        private ChatMemberRepositoryInterface $chatMemberRepository,
        private TranslatorInterface $translator
    ) {}

    /**
     * @throws Exception
     */
    public function __invoke(
        DemoteReputationCommand $command
    ): ChatClientResultDTO {
        try {
            $who = $command->chocoData->who;

            $repliedMessage = $command->data->getUpdateMessageReplyToMessage();

            $repliedUser = $repliedMessage->getFrom();

            if (null === $repliedUser) {
                throw new Exception(
                    'DemoteReputationCommandHandler $repliedUser is NULL'
                );
            }

            if (
                $command->data->getUpdateMessageFrom()->getSourceUserId() ===
                $repliedUser->getSourceUserId()
            ) {
                throw new ChatMemberException(
                    $this->translator->trans('ChatMemberReputationSelfDemotion')
                );
            }

            $repliedChatMember = $this->chatMemberRepository->findChatMember(
                $command->getSourceChatId(),
                $repliedUser->getSourceUserId(),
                $command->data->getUpdate()
                    ->getSourceServiceName()
            );

            for ($i = 0; $i < $command->getPromoteReputationValue(); $i++) {
                try {
                    $repliedChatMember->demoteReputation($who);
                } catch (ChatMemberReputationException) {
                    throw new ChatMemberException(
                        $this->translator->trans('ChatMemberQuotaExceeded')
                    );
                }
            }

            $command->chocoData->client->sendMessage(
                $command->getSourceChatId(),
                sprintf(
                    $this->translator->trans('ChangedReputation'),
                    $command->chocoData->client
                        ->getChatMemberMentionString(
                            $repliedUser->getFirstName(),
                            $repliedUser
                        ),
                    $repliedChatMember->getReputation()
                )
            );

            return ChatClientResultDTO::success();
        } catch (ChatMemberException $e) {
            $command->chocoData->client->sendMessage(
                $command->getSourceChatId(),
                $e->getMessage()
            );

            return ChatClientResultDTO::fail(
                $e->getMessage(),
                true
            );
        }
    }
}
