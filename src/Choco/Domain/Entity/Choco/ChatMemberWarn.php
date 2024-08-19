<?php

declare(strict_types=1);

namespace App\Choco\Domain\Entity\Choco;

use App\Choco\Domain\Event\ChatMember\ExpiredWarnNotifyEvent;
use App\Shared\Domain\Entity\Aggregate;
use App\Shared\Domain\Service\UlidService;
use Carbon\Carbon;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity, ORM\Table(name: 'choco_chat_member_warn')]
class ChatMemberWarn
    extends Aggregate
{
    #[
        ORM\Id,
        ORM\Column(type: Types::STRING),
        ORM\GeneratedValue(strategy: 'NONE')
    ]
    private readonly string $id;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $expired = false;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: ChatMember::class, inversedBy: 'warns')]
        #[ORM\JoinColumn(
            name: 'warned_chat_member_id',
            referencedColumnName: 'id'
        )]
        private readonly ChatMember $warned,
        #[ORM\ManyToOne(
            targetEntity: ChatMember::class,
            inversedBy: 'issuedWarnings'
        )]
        #[ORM\JoinColumn(
            name: 'creator_chat_member_id',
            referencedColumnName: 'id'
        )]
        private readonly ChatMember $creator,
        #[ORM\ManyToOne(targetEntity: ChocoChat::class, inversedBy: 'warns')]
        #[ORM\JoinColumn(name: 'chat_id', referencedColumnName: 'id')]
        private readonly ChocoChat $chat,
        #[ORM\Column(type: Types::STRING)]
        private readonly string $reason,
        #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
        private readonly DateTimeImmutable $expiresAt,
    ) {
        $this->id = UlidService::generate();

        $this->createdAt = Carbon::now()->toDateTimeImmutable();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isExpired(): bool
    {
        return $this->expired;
    }

    public function expire(): void
    {
        $this->expired = true;
        $this->raise(
            new ExpiredWarnNotifyEvent(
                $this->warned->getId(),
                $this->getId()
            )
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getWarned(): ChatMember
    {
        return $this->warned;
    }

    public function getWarnedFirstname(): string
    {
        return $this->warned->getUser()->getUpdateUser()->getFirstName();
    }

    public function getCreator(): ChatMember
    {
        return $this->creator;
    }

    public function getCreatorFirstname(): string
    {
        return $this->creator->getUser()->getUpdateUser()->getFirstName();
    }

    public function getChat(): ChocoChat
    {
        return $this->chat;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }
}
