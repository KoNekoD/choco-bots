<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Telemetry\ChatMessages\PerChatMember;

use App\Choco\Application\ChatCommand\Telemetry\ChatMessages\PerChatMember\Service\ChatMemberTelemetryProvider;
use App\Choco\Domain\Enum\ChatMemberStatusEnum;
use App\Choco\Domain\Exception\ChocoEntity\ChatMemberKickedOrLeftException;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Shared\Application\Command\CommandHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class MyStatisticCommandHandler
    implements CommandHandlerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
        private ChatMemberTelemetryProvider $telemetryProvider,
    ) {}

    public function __invoke(MyStatisticCommand $command): ChatClientResultDTO
    {
        $who = $command->chocoData->who;

        try {
            $text = $this->telemetryProvider->produce(
                $who,
                $command->chocoData->client->getChatMemberMentionString(
                    $who->getUserFirstName(),
                    $who->getUser()->getUpdateUser()
                ),
                $command->chocoData->client->getChatMemberLinkString(
                    $who->getUser()
                )
            );
            $command->chocoData->client->sendMessage(
                $command->getSourceChatId(),
                $text,
                parseMode: 'HTML'
            );
        } catch (ChatMemberKickedOrLeftException) {
            $chatMemberStatus = $who->getStatus();
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
                        $who->getId(),
                        $who->getStatus()->name
                    ),
                    true
                );
            }
        }

        return ChatClientResultDTO::success();
    }
}
