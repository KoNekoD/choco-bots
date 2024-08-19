<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Moderation\RankAssignment;

use App\Choco\Domain\Exception\ChocoEntity\ChatMemberException;
use App\Choco\Domain\Repository\ChatMemberRepositoryInterface;
use App\Choco\Domain\Service\ChatMemberAuthenticator;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;
use App\Shared\Application\Command\CommandHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class PromoteRankCommandHandler
    implements CommandHandlerInterface
{
    public function __construct(
        private ChatMemberRepositoryInterface $chatMemberRepository,
        private ChatMemberAuthenticator $memberAuthenticator,
        private TranslatorInterface $translator
    ) {}

    /** @throws ChatClientAPIException */
    public function __invoke(PromoteRankCommand $command): ChatClientResultDTO
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
            $this->memberAuthenticator->authenticateRankPrimitive(
                $who,
                $target->getRank()->getRankValuePrimitive(
                ) + $command->getPromoteRanksValue()
            );
            $target->rankPromote($command->getPromoteRanksValue());

            $command->chocoData->client->sendMessage(
                $command->getSourceChatId(),
                sprintf(
                    $this->translator->trans('ChangedRank'),
                    $command->getTargetUsername(),
                    $target->getRank()->getRankValue()->name
                )
            );

            return ChatClientResultDTO::success();
        } catch (ChatMemberException $e) {
            $command->chocoData->client->sendMessage(
                $command->getSourceChatId(),
                $e->getMessage()
            );

            return ChatClientResultDTO::fail($e->getMessage(), true);
        }
    }
}
