<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Moderation\GettingInformation;

use App\Choco\Domain\Repository\ChatMemberRepositoryInterface;
use App\Choco\Domain\Service\ChatMemberAuthenticator;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Shared\Application\Command\CommandHandlerInterface;

final readonly class WhoAdminCommandHandler
    implements CommandHandlerInterface
{
    public function __construct(
        private ChatMemberRepositoryInterface $chatMemberRepository
    ) {}

    public function __invoke(WhoAdminCommand $command): ChatClientResultDTO
    {
        $members = $this->chatMemberRepository->getChatMembersWithPrivileges(
            $command->getSourceChatId(),
            $command->data->getUpdate()->getSourceServiceName()
        );
        $result = 'Список участников с привилегиями:';
        foreach ($members as $member) {
            $user = $member->getUser();
            $memberName = $user->getUpdateUser()->getFirstName();
            $memberPrefix = ChatMemberAuthenticator::getChatMemberPrefix(
                $member->getRank()->getRankValue()
            );
            $mention = $command->chocoData->client->getChatMemberMentionString(
                $memberName,
                $user->getUpdateUser()
            );
            $result .= "\n $memberPrefix $mention {$member->getRank()->getRankValue()->name}";
        }
        $command->chocoData->client->sendMessage(
            $command->getSourceChatId(),
            $result
        );

        return ChatClientResultDTO::success();
    }
}
