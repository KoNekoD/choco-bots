<?php

declare(strict_types=1);

namespace App\Choco\Infrastructure\Factory;

use App\Choco\Domain\Entity\Choco\ChatMember;
use App\Choco\Domain\Entity\Choco\ChatMemberRank;
use App\Choco\Domain\Entity\Choco\ChocoChat;
use App\Choco\Domain\Entity\Choco\ChocoUser;
use App\Choco\Domain\Enum\ChatMemberRankStatusEnum;
use App\Choco\Domain\Enum\ChatMemberStatusEnum;
use App\Choco\Domain\Event\ChatMember\NewMemberNotifyEvent;
use App\Choco\Domain\Exception\ChocoEntity\ChatMemberException;
use App\Choco\Domain\Repository\ChatMemberRepositoryInterface;
use App\Choco\Domain\Repository\ChatMemberWarnRepositoryInterface;
use App\Main\Domain\Exception\UpdateEntities\UpdateChatNotFoundException;
use App\Main\Domain\Exception\UpdateEntities\UpdateUserNotFoundException;
use App\Main\Infrastructure\ChatClientAPI\Telegram\TelegramClient;
use TelegramBot\Api\Types as TG;

final readonly class ChatMemberFactory
{
    public function __construct(
        private ChatMemberRepositoryInterface $chatMemberRepository,
        private ChatMemberWarnRepositoryInterface $chatMemberWarnRepository,
        private ChocoFactory $chocoFactory,
    ) {}

    /**
     * @throws ChatMemberException
     * @throws UpdateChatNotFoundException
     * @throws UpdateUserNotFoundException
     */
    public function createOrGetAndUpdateChatMemberFromTelegramResponse(
        TG\ChatMember $m,
        int $sourceChatId
    ): ChatMember {
        switch ($m->getStatus()) {
            case 'creator':
                $status = ChatMemberStatusEnum::Creator;
                $rankStatus = ChatMemberRankStatusEnum::Creator;
                break;
            case 'administrator':
                $status = ChatMemberStatusEnum::Administrator;
                $rankStatus = ChatMemberRankStatusEnum::SeniorAdministrator;
                break;
            case 'member':
                $status = ChatMemberStatusEnum::Member;
                $rankStatus = ChatMemberRankStatusEnum::Member;
                break;
            case 'left':
                $status = ChatMemberStatusEnum::Left;
                $rankStatus = ChatMemberRankStatusEnum::Member;
                break;
            case 'kicked':
                $status = ChatMemberStatusEnum::Kicked;
                $rankStatus = ChatMemberRankStatusEnum::Member;
                break;
            case 'restricted':
                $status = ChatMemberStatusEnum::Restricted;
                $rankStatus = ChatMemberRankStatusEnum::Member;
                break;
            default:
                $allowed = '{creator, administrator, member, left, kicked}';
                throw new ChatMemberException(
                    'Unknown chat member status. '.sprintf(
                        'Can be %s, got %s',
                        $allowed,
                        $m->getStatus()
                    )
                );
        }

        return $this->createOrGetAndUpdateChatMember(
            $this->chocoFactory->createOrGetChat(
                $sourceChatId,
                TelegramClient::getClientAdapterName()
            ),
            $this->chocoFactory->createOrGetUser(
                (int)$m->getUser()->getId(),
                TelegramClient::getClientAdapterName()
            ),
            TelegramClient::getClientAdapterName(),
            $status,
            $m->getCustomTitle(),
            $m->getIsAnonymous(),
            $m->getUntilDate(),
            $m->getCanBeEdited(),
            $m->getCanPostMessages(),
            $m->getCanEditMessages(),
            $m->getCanDeleteMessages(),
            $m->getCanPromoteMembers(),
            $m->getCanChangeInfo(),
            $m->getCanInviteUsers(),
            $m->getCanPinMessages(),
            $m->getCanSendMessages(),
            $m->getCanSendMediaMessages(),
            $m->getCanSendPolls(),
            $m->getCanSendOtherMessages(),
            $m->getCanAddWebPagePreviews(),
            $m->getCanManageChat(),
            $rankStatus
        );
    }

    public function createOrGetAndUpdateChatMember(
        ChocoChat $chat,
        ChocoUser $user,
        string $sourceServiceName,
        ChatMemberStatusEnum $status,
        ?string $customTitle,
        ?bool $isAnonymous,
        ?int $untilDate,
        ?bool $canBeEdited,
        ?bool $canPostMessages,
        ?bool $canEditMessages,
        ?bool $canDeleteMessages,
        ?bool $canPromoteMembers,
        ?bool $canChangeInfo,
        ?bool $canInviteUsers,
        ?bool $canPinMessages,
        ?bool $canSendMessages,
        ?bool $canSendMediaMessages,
        ?bool $canSendPolls,
        ?bool $canSendOtherMessages,
        ?bool $canAddWebPagePreviews,
        ?bool $canManageChat,
        ChatMemberRankStatusEnum $memberRankStatusEnum,
    ): ChatMember {
        try {
            $chatMember = $this->chatMemberRepository->findChatMember(
                $chat->getSourceChatId(),
                $user->getSourceUserId(),
                $sourceServiceName
            );

            $chatMember->updateMemberInformation(
                $status,
                $customTitle,
                $isAnonymous,
                $untilDate,
                $canBeEdited,
                $canPostMessages,
                $canEditMessages,
                $canDeleteMessages,
                $canPromoteMembers,
                $canChangeInfo,
                $canInviteUsers,
                $canPinMessages,
                $canSendMessages,
                $canSendMediaMessages,
                $canSendPolls,
                $canSendOtherMessages,
                $canAddWebPagePreviews,
                $canManageChat
            );

            $expiredWarns = $chatMember->checkExpiredWarnings();
            foreach ($expiredWarns as $expiredWarn) {
                // Executing events
                $this->chatMemberWarnRepository->add($expiredWarn);
            }

            if (ChatMemberRankStatusEnum::Member !== $memberRankStatusEnum) {
                // Sync as source service
                $chatMember->rankUpdate($memberRankStatusEnum);
            }

            $this->chatMemberRepository->add($chatMember);

            return $chatMember;
        } catch (ChatMemberException) {
            $entity = new ChatMember(
                $chat,
                $user,
                $status,
                $customTitle,
                $isAnonymous,
                $untilDate,
                $canBeEdited,
                $canPostMessages,
                $canEditMessages,
                $canDeleteMessages,
                $canPromoteMembers,
                $canChangeInfo,
                $canInviteUsers,
                $canPinMessages,
                $canSendMessages,
                $canSendMediaMessages,
                $canSendPolls,
                $canSendOtherMessages,
                $canAddWebPagePreviews,
                $canManageChat,
            );

            $entity->initRank(
                $this->createChatMemberRank(
                    $entity,
                    $memberRankStatusEnum
                )
            );

            if (
                $chat->getConfiguration()
                    ->isNotifyAboutNewChatMemberInSystem()
            ) {
                $entity->raise(
                    new NewMemberNotifyEvent(
                        $entity->getId()
                    )
                );
            }

            $this->chatMemberRepository->add($entity);

            return $entity;
        }
    }

    private function createChatMemberRank(
        ChatMember $member,
        ChatMemberRankStatusEnum $rankStatus
    ): ChatMemberRank {
        $entity = new ChatMemberRank($member);
        $entity->setRankValue($rankStatus);
        $this->chatMemberRepository->addRank($entity);

        return $entity;
    }
}
