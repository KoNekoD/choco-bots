<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Settings\ChatConfiguration\NotifyAboutNewChatMemberInSystem;

use App\Choco\Domain\ChatConfiguration\ChatConfigurationRights;
use App\Choco\Domain\Exception\ChocoEntity\ChatMemberException;
use App\Choco\Domain\Service\ChatMemberAuthenticator;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Shared\Application\Command\CommandHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class EnableCommandHandler
    implements CommandHandlerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
        private ChatMemberAuthenticator $memberAuthenticator
    ) {}

    public function __invoke(EnableCommand $command): ChatClientResultDTO
    {
        try {
            $who = $command->chocoData->who;
            $this->memberAuthenticator->authenticateRankPrimitive(
                $who,
                ChatConfigurationRights::CAN_MANAGE_CHAT_CONFIGURATION->value
            );

            $command->chocoData->chat
                ->manageConfiguration(
                    notifyAboutNewChatMember: true
                );

            $command->chocoData->client->sendMessage(
                $command->getSourceChatId(),
                $this->translator->trans('EnabledNewMemberInSystemNotify')
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
