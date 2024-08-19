<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Moderation\Bans\Warns;

use App\Choco\Domain\Exception\ChocoEntity\ChatMemberException;
use App\Choco\Domain\Factory\ChatMemberWarnFactory;
use App\Choco\Domain\Repository\ChatMemberRepositoryInterface;
use App\Choco\Domain\Service\ChatMemberAuthenticator;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;
use App\Shared\Application\Command\CommandHandlerInterface;
use DomainException;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class GiveWarnCommandHandler
    implements CommandHandlerInterface
{
    public function __construct(
        private ChatMemberRepositoryInterface $chatMemberRepository,
        private ChatMemberAuthenticator $memberAuthenticator,
        private TranslatorInterface $translator,
        protected ChatMemberWarnFactory $chatMemberWarnFactory,
    ) {}

    /** @throws ChatClientAPIException */
    public function __invoke(GiveWarnCommand $command): ChatClientResultDTO
    {
        try {
            $who = $command->chocoData->who;
            $target = $this->chatMemberRepository->findChatMemberByFirstMentionOrUsername(
                $command->data->getUpdate(),
                $command->getTargetUsername()
            );
            $this->memberAuthenticator->authenticateRank(
                $who,
                $target->getRank()->getRankValue(),
                $target
            );

            $newWarn = $this->chatMemberWarnFactory->createChatMemberWarn(
                $target,
                $who,
                $command->getWarnReason(),
                $command->getWarnExpireDateTime()
            );

            $command->chocoData->client->sendMessage(
                $command->getSourceChatId(),
                sprintf(
                    $this->translator->trans('ChatMemberSuccessfullyWarned'),
                    $command->chocoData->client->getChatMemberMentionString(
                        $command->getTargetUsername(),
                        $target->getUser()->getUpdateUser()
                    ),
                    $newWarn->getExpiresAt()->format('Y-m-d H:i:s'),
                    $newWarn->getReason()
                )
            );
        } catch (ChatMemberException|DomainException $e) {
            $command->chocoData->client->sendMessage(
                $command->getSourceChatId(),
                $e->getMessage()
            );

            return ChatClientResultDTO::fail($e->getMessage(), true);
        }

        return ChatClientResultDTO::success();
    }
}
