<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Moderation\Bans\Warns;

use App\Choco\Domain\Exception\ChocoEntity\ChatMemberException;
use App\Choco\Domain\Factory\ChatMemberWarnFactory;
use App\Choco\Domain\Repository\ChatMemberRepositoryInterface;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;
use App\Shared\Application\Command\CommandHandlerInterface;

final readonly class GetChatWarnsCommandHandler
    implements CommandHandlerInterface
{
    public function __construct(
        private ChatMemberRepositoryInterface $chatMemberRepository,
        protected ChatMemberWarnFactory $chatMemberWarnFactory,
    ) {}

    /** @throws ChatClientAPIException */
    public function __invoke(GetChatWarnsCommand $command): ChatClientResultDTO
    {
        try {
            $chat = $command->chocoData->chat;
            if ($command->getTargetUsername()) {
                $memberFilter = $this->chatMemberRepository->findChatMemberByFirstMentionOrUsername(
                    $command->data->getUpdate(),
                    $command->getTargetUsername()
                );
                $warns = $chat->getWarnsByWarnedMember($memberFilter);
                $resultString = "Список предупреждений участника @{$command->getTargetUsername()}: \n";
                foreach ($warns as $warn) {
                    if ($warn->isExpired()) {
                        continue;
                    }
                    $resultString .= sprintf(
                        'От: %s, Причина: %s, Истекает: %s.'."\n",
                        $warn->getCreatorFirstname(),
                        $warn->getReason(),
                        $warn->getExpiresAt()->format('Y-m-d H:i:s')
                    );
                }
            } else {
                $warns = $chat->getLastFiveWarns();
                $resultString = "Список предупреждений: \n";
                foreach ($warns as $warn) {
                    if ($warn->isExpired()) {
                        continue;
                    }
                    $resultString .= sprintf(
                        'Предупреждение для %s, от: %s, Истекает: %s.'."\n",
                        $warn->getWarnedFirstname(),
                        $warn->getCreatorFirstName(),
                        $warn->getExpiresAt()->format('Y-m-d H:i:s')
                    );
                }
            }

            $command->chocoData->client->sendMessage(
                $command->getSourceChatId(),
                $resultString
            );
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
