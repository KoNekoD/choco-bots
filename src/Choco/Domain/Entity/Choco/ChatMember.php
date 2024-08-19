<?php

declare(strict_types=1);

namespace App\Choco\Domain\Entity\Choco;

use App\Choco\Domain\Enum\ChatMemberRankStatusEnum;
use App\Choco\Domain\Enum\ChatMemberStatusEnum;
use App\Choco\Domain\Exception\ChocoEntity\ChatMemberException;
use App\Choco\Domain\Exception\ChocoEntity\ChatMemberKickedOrLeftException;
use App\Choco\Domain\Exception\ChocoEntity\ChatMemberReputationException;
use App\Choco\Domain\Exception\Marry\MarryException;
use App\Shared\Domain\Entity\Aggregate;
use App\Shared\Domain\Service\UlidService;
use Carbon\Carbon;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

/**
 * @TODO Move general login to Main->Update
 */
#[ORM\Entity, ORM\Table(name: 'choco_chat_member')]
class ChatMember
    extends Aggregate
{
    final public const REPUTATION_CHANGE_QUOTA_DEFAULT = 10;

    #[
        ORM\Id,
        ORM\Column(type: 'string', length: 26),
        ORM\GeneratedValue(strategy: 'NONE')
    ]
    private readonly string $id;

    #[ORM\OneToOne(inversedBy: 'member', targetEntity: ChatMemberRank::class)]
    #[ORM\JoinColumn(name: 'rank_id', referencedColumnName: 'id')]
    private ?ChatMemberRank $rank = null;

    /** @var Collection<ChatMemberWarn> $warns */
    #[ORM\OneToMany(
        mappedBy: 'warned',
        targetEntity: ChatMemberWarn::class,
        cascade: ['persist', 'remove']
    )]
    private Collection $warns;

    /** @var Collection<ChatMemberWarn> $issuedWarnings */
    #[ORM\OneToMany(
        mappedBy: 'creator',
        targetEntity: ChatMemberWarn::class,
        cascade: ['persist', 'remove']
    )]
    private Collection $issuedWarnings;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $sinceSpentTime = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $reputation;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $reputationChangeQuotaLastUpdated;

    #[ORM\Column(type: Types::INTEGER)]
    private int $reputationChangeQuota;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: ChocoChat::class, inversedBy: 'members')]
        #[ORM\JoinColumn(name: 'chat_id', referencedColumnName: 'id')]
        private readonly ChocoChat $chat,
        #[ORM\ManyToOne(targetEntity: ChocoUser::class, inversedBy: 'chats')]
        #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
        private readonly ChocoUser $user,
        #[ORM\Column(type: 'smallint', enumType: ChatMemberStatusEnum::class)]
        private ChatMemberStatusEnum $status,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $customTitle,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $isAnonymous,
        #[ORM\Column(type: 'integer', nullable: true)]
        private ?int $untilDate,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $canBeEdited,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $canPostMessages,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $canEditMessages,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $canDeleteMessages,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $canPromoteMembers,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $canChangeInfo,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $canInviteUsers,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $canPinMessages,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $canSendMessages,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $canSendMediaMessages,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $canSendPolls,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $canSendOtherMessages,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $canAddWebPagePreviews,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $canManageChat,
    ) {
        $this->id = UlidService::generate();
        $this->createdAt = Carbon::now()->toDateTimeImmutable();

        if (
            ChatMemberStatusEnum::Kicked !== $this
                ->status && ChatMemberStatusEnum::Left !== $this->status
        ) {
            $this->sinceSpentTime = Carbon::now()->toDateTimeImmutable();
        }

        $this->warns = new ArrayCollection();
        $this->issuedWarnings = new ArrayCollection();

        $this->reputation = 0;
        $this->reputationChangeQuota = self::REPUTATION_CHANGE_QUOTA_DEFAULT;

        $this->reputationChangeQuotaLastUpdated = Carbon::now()
            ->toDateTimeImmutable();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @throws ChatMemberKickedOrLeftException
     */
    public function getSinceSpentTime(): DateTimeImmutable
    {
        if (null === $this->sinceSpentTime) {
            throw new ChatMemberKickedOrLeftException();
        }

        return $this->sinceSpentTime;
    }

    /** @throws ChatMemberReputationException */
    public function promoteReputation(self $who): void
    {
        if ($who->getReputationChangeQuota() > 0) {
            $this->reputation++;
            $who->decreaseReputationQuota();
        } else {
            throw new ChatMemberReputationException('Quota exceeded');
        }
    }

    private function getReputationChangeQuota(): int
    {
        return $this->reputationChangeQuota;
    }

    private function decreaseReputationQuota(): void
    {
        $this->reputationChangeQuota--;
    }

    /** @throws ChatMemberReputationException */
    public function demoteReputation(self $who): void
    {
        if ($who->getReputationChangeQuota() > 0) {
            $this->reputation--;
            $who->decreaseReputationQuota();
        } else {
            throw new ChatMemberReputationException('Quota exceeded');
        }
    }

    public function getReputation(): int
    {
        return $this->reputation;
    }

    public function getChat(): ChocoChat
    {
        return $this->chat;
    }

    public function getUser(): ChocoUser
    {
        return $this->user;
    }

    public function getUserUsernameOrFirstName(): string
    {
        return $this->getUserUsername() ?? $this->getUserFirstName();
    }

    public function getUserUsername(): ?string
    {
        return $this->user->getUpdateUser()->getUsername();
    }

    public function getUserFirstName(): string
    {
        return $this->user->getUpdateUser()->getFirstName();
    }

    public function getSourceServiceName(): string
    {
        return $this->user->getSourceServiceName();
    }

    public function getStatus(): ChatMemberStatusEnum
    {
        return $this->status;
    }

    public function getCustomTitle(): ?string
    {
        return $this->customTitle;
    }

    public function getIsAnonymous(): ?bool
    {
        return $this->isAnonymous;
    }

    public function getUntilDate(): ?int
    {
        return $this->untilDate;
    }

    public function getCanBeEdited(): ?bool
    {
        return $this->canBeEdited;
    }

    public function getCanPostMessages(): ?bool
    {
        return $this->canPostMessages;
    }

    public function getCanEditMessages(): ?bool
    {
        return $this->canEditMessages;
    }

    public function getCanDeleteMessages(): ?bool
    {
        return $this->canDeleteMessages;
    }

    public function getCanPromoteMembers(): ?bool
    {
        return $this->canPromoteMembers;
    }

    public function getCanChangeInfo(): ?bool
    {
        return $this->canChangeInfo;
    }

    public function getCanInviteUsers(): ?bool
    {
        return $this->canInviteUsers;
    }

    public function getCanPinMessages(): ?bool
    {
        return $this->canPinMessages;
    }

    public function getCanSendMessages(): ?bool
    {
        return $this->canSendMessages;
    }

    public function getCanSendMediaMessages(): ?bool
    {
        return $this->canSendMediaMessages;
    }

    public function getCanSendPolls(): ?bool
    {
        return $this->canSendPolls;
    }

    public function getCanSendOtherMessages(): ?bool
    {
        return $this->canSendOtherMessages;
    }

    public function getCanAddWebPagePreviews(): ?bool
    {
        return $this->canAddWebPagePreviews;
    }

    public function getCanManageChat(): ?bool
    {
        return $this->canManageChat;
    }

    public function getWarns(): Collection
    {
        return $this->warns;
    }

    public function getIssuedWarnings(): Collection
    {
        return $this->issuedWarnings;
    }

    /**
     * @throws ChatMemberException
     */
    public function getWarnById(string $warnId): ChatMemberWarn
    {
        foreach ($this->warns as $warn) {
            if ($warn->getId() === $warnId) {
                return $warn;
            }
        }
        throw new ChatMemberException(
            "ChocoChat member warn with id: $warnId not found"
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function initRank(ChatMemberRank $rank): void
    {
        $this->rank = $rank;
    }

    public function rankPromote(int $rankIncrement): void
    {
        $this->getRank()->promote($rankIncrement);
    }

    public function getRank(): ChatMemberRank
    {
        if (null === $this->rank) {
            throw new DomainException('ChatMember must should be exist');
        }

        return $this->rank;
    }

    public function rankDemote(int $rankDecrement): void
    {
        $this->getRank()->demote($rankDecrement);
    }

    public function rankUpdate(ChatMemberRankStatusEnum $rank): void
    {
        $this->getRank()->setRankValue($rank);
    }

    public function updateMemberInformation(
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
    ): void {
        $this->customTitle = $customTitle;
        $this->isAnonymous = $isAnonymous;
        $this->untilDate = $untilDate;
        $this->canBeEdited = $canBeEdited;
        $this->canPostMessages = $canPostMessages;
        $this->canEditMessages = $canEditMessages;
        $this->canDeleteMessages = $canDeleteMessages;
        $this->canPromoteMembers = $canPromoteMembers;
        $this->canChangeInfo = $canChangeInfo;
        $this->canInviteUsers = $canInviteUsers;
        $this->canPinMessages = $canPinMessages;
        $this->canSendMessages = $canSendMessages;
        $this->canSendMediaMessages = $canSendMediaMessages;
        $this->canSendPolls = $canSendPolls;
        $this->canSendOtherMessages = $canSendOtherMessages;
        $this->canAddWebPagePreviews = $canAddWebPagePreviews;
        $this->canManageChat = $canManageChat;

        $this->updateStatus($status);
        $this->tryUpdateReputationChangeQuota();
    }

    private function updateStatus(ChatMemberStatusEnum $status): void
    {
        $this->status = $status;

        if (
            ChatMemberStatusEnum::Kicked === $this->status
            || ChatMemberStatusEnum::Left === $this->status
        ) {
            $this->sinceSpentTime = null;
        } elseif (null === $this->sinceSpentTime) {
            $this->sinceSpentTime = Carbon::now()->toDateTimeImmutable();
        }
    }

    private function tryUpdateReputationChangeQuota(): void
    {
        $nowTimestamp = Carbon::now()->toDateTimeImmutable()->getTimestamp();

        $nextQuotaUpdateTimestamp = $this->reputationChangeQuotaLastUpdated
            ->modify('+10 hours')
            ->getTimestamp();

        if ($nowTimestamp > $nextQuotaUpdateTimestamp) {
            $this->reputationChangeQuota =
                self::REPUTATION_CHANGE_QUOTA_DEFAULT;

            $this->reputationChangeQuotaLastUpdated = Carbon::now()
                ->toDateTimeImmutable();
        }
    }

    /** @return ChatMemberWarn[] */
    public function checkExpiredWarnings(): array
    {
        $nowTimestamp = Carbon::now()->toDateTimeImmutable()->getTimestamp();
        $expiredWarns = [];
        foreach ($this->warns as $warn) {
            if (
                $nowTimestamp > $warn->getExpiresAt()->getTimestamp()
                && !$warn->isExpired()
            ) {
                $warn->expire();
                $expiredWarns[] = $warn;
            }
        }

        return $expiredWarns;
    }

    /** @throws MarryException */
    public function acceptMarry(): void
    {
        $this->user->acceptMarry(
            $this->chat->getSourceChatId()
        );
    }

    public function getMarry(): ?Marry
    {
        return $this->user->getMarry();
    }

    public function isMarried(): bool
    {
        return $this->user->isMarried();
    }
}
