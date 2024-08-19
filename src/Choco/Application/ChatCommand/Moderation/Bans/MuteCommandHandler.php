<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Moderation\Bans;

use App\Choco\Domain\ChatConfiguration\ChatConfigurationRights;
use App\Choco\Domain\Exception\ChocoEntity\ChatMemberException;
use App\Choco\Domain\Repository\ChatMemberRepositoryInterface;
use App\Choco\Domain\Service\ChatMemberAuthenticator;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Main\Domain\Exception\BaseException;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;
use App\Shared\Application\Command\CommandHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class MuteCommandHandler
    implements CommandHandlerInterface
{
    public function __construct(
        private ChatMemberRepositoryInterface $chatMemberRepository,
        private ChatMemberAuthenticator $memberAuthenticator,
        private TranslatorInterface $translator
    ) {}

    /**
     * @throws ChatClientAPIException
     * @throws BaseException
     */
    public function __invoke(MuteCommand $command): ChatClientResultDTO
    {
        try {
            $who = $command->chocoData->who;
            $this->memberAuthenticator->authenticateRankPrimitive(
                $who,
                ChatConfigurationRights::CAN_MUTE->value
            );
            $target = $this->chatMemberRepository->findChatMemberByFirstMentionOrUsername(
                $command->data->getUpdate(),
                $command->getTargetUsername()
            );
            $this->memberAuthenticator->authenticateRank(
                $who,
                $target->getRank()->getRankValue(),
                $target
            );
            $result = $command->chocoData->client->muteChatMember(
                $target,
                $command->getMuteReason()
            );

            if ($result->isOk) {
                $command->chocoData->client->sendMessage(
                    $command->getSourceChatId(),
                    sprintf(
                        $this->translator->trans('ChatMemberSuccessfullyMuted'),
                        $command->chocoData->client->getChatMemberMentionString(
                            $command->getTargetUsername(),
                            $target->getUser()->getUpdateUser()
                        ),
                        "{$target->getChat()->getDefaultMuteTimeInSeconds()} секунд"
                    )
                );
            } else {
                $command->chocoData->client->sendMessage(
                    $command->getSourceChatId(),
                    sprintf(
                        $this->translator->trans('CommandFailedMessage'),
                        $result->getError()
                    )
                );

                return ChatClientResultDTO::fail($result->getError(), true);
            }
        } catch (ChatMemberException $e) {
            $command->chocoData->client->sendMessage(
                $command->getSourceChatId(),
                $e->getMessage()
            );

            return ChatClientResultDTO::fail($e->getMessage(), true);
        }

        return ChatClientResultDTO::success();
    }
}
