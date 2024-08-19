<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Telemetry\ChatMessages\PerChatMember;

use App\Choco\Application\ChatCommand\Telemetry\ChatMessages\PerChatMember\Service\ChatMemberTelemetryProvider;
use App\Choco\Domain\Enum\ChatMemberStatusEnum;
use App\Choco\Domain\Exception\ChocoEntity\ChatMemberException;
use App\Choco\Domain\Exception\ChocoEntity\ChatMemberKickedOrLeftException;
use App\Choco\Domain\Repository\ChatMemberRepositoryInterface;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;
use App\Shared\Application\Command\CommandHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ChatMemberStatisticCommandHandler
    implements CommandHandlerInterface
{
    public function __construct(
        private ChatMemberRepositoryInterface $chatMemberRepository,
        private TranslatorInterface $translator,
        private ChatMemberTelemetryProvider $telemetryProvider,
    ) {}

    public function __invoke(
        ChatMemberStatisticCommand $command
    ): ChatClientResultDTO {
        try {
            $target = $this->chatMemberRepository
                ->findChatMemberByFirstMentionOrUsername(
                    $command->data->getUpdate(),
                    $command->getTargetUsername()
                );

            try {
                $text = $this->telemetryProvider->produce(
                    $target,
                    $command->chocoData->client
                        ->getChatMemberMentionString(
                            $target->getUserFirstName(),
                            $target->getUser()->getUpdateUser()
                        ),
                    $command->chocoData->client
                        ->getChatMemberLinkString(
                            $target->getUser()
                        )
                );
                $command->chocoData->client->sendMessage(
                    $command->getSourceChatId(),
                    $text,
                    parseMode: 'HTML'
                );
            } catch (ChatMemberKickedOrLeftException) {
                $chatMemberStatus = $target->getStatus();
                if (ChatMemberStatusEnum::Kicked === $chatMemberStatus) {
                    $command->chocoData->client->sendMessage(
                        $command->getSourceChatId(),
                        $this->translator->trans('ChatMemberIsKicked')
                    );
                } elseif (ChatMemberStatusEnum::Left === $chatMemberStatus) {
                    $command->chocoData->client->sendMessage(
                        $command->getSourceChatId(),
                        $this->translator->trans('ChatMemberIsLeft')
                    );
                } else {
                    return ChatClientResultDTO::fail(
                        sprintf(
                            'Unknown status of chat member id: %s status: %s',
                            $target->getId(),
                            $target->getStatus()->name
                        ),
                        true
                    );
                }
            }

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
        } catch (ChatClientAPIException $e) {
            return ChatClientResultDTO::fatal($e);
        }
    }
}
