<?php

declare(strict_types=1);

namespace App\Choco\Domain\Service;

use App\Choco\Domain\Entity\Choco\ChatMember;
use App\Choco\Domain\Enum\ChatMemberRankStatusEnum;
use App\Choco\Domain\Enum\ChatMemberStatusEnum;
use App\Choco\Domain\Exception\ChocoEntity\ChatMemberException;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ChatMemberAuthenticator
{
    public function __construct(
        private TranslatorInterface $translator
    ) {}

    public static function getChatMemberPrefix(
        ChatMemberRankStatusEnum $rank
    ): string {
        return match ($rank) {
            ChatMemberRankStatusEnum::Member => '🤡',
            ChatMemberRankStatusEnum::JuniorModerator => '⚡️',
            ChatMemberRankStatusEnum::SeniorModerator => '🥉',
            ChatMemberRankStatusEnum::JuniorAdministrator => '🥈',
            ChatMemberRankStatusEnum::SeniorAdministrator => '🥇',
            ChatMemberRankStatusEnum::Creator => '💎',
        };
    }

    /** @throws ChatMemberException */
    public function authenticateRank(
        ChatMember $who,
        ChatMemberRankStatusEnum $requiredAccessRank,
        ChatMember $target,
    ): void {
        $targetStatus = $target->getStatus();

        if (ChatMemberStatusEnum::Left === $targetStatus) {
            throw new ChatMemberException(
                $this->translator->trans('AccessDeniedChatMemberIsLeaved')
            );
        }

        if (ChatMemberStatusEnum::Kicked === $targetStatus) {
            throw new ChatMemberException(
                $this->translator->trans('AccessDeniedChatMemberIsKicked')
            );
        }

        if (ChatMemberStatusEnum::Administrator === $targetStatus) {
            throw new ChatMemberException(
                $this->translator->trans(
                    'AccessDeniedChatMemberIsAdministrator'
                )
            );
        }

        if (ChatMemberStatusEnum::Creator === $targetStatus) {
            throw new ChatMemberException(
                $this->translator->trans('AccessDeniedChatMemberIsCreator')
            );
        }

        if (
            $who->getRank()
                ->getRankValue()->value <= $requiredAccessRank->value
        ) {
            throw new ChatMemberException(
                $this->translator->trans('ChatMemberAccessDenied')
            );
        }
    }

    /** @throws ChatMemberException */
    public function authenticateRankPrimitive(
        ChatMember $who,
        int $requiredAccessRank
    ): void {
        if ($who->getRank()->getRankValue()->value <= $requiredAccessRank) {
            throw new ChatMemberException(
                $this->translator->trans('ChatMemberAccessDenied')
            );
        }
    }
}
