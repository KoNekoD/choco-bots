<?php

declare(strict_types=1);

namespace App\Choco\Domain\Entity\Choco;

use App\Choco\Domain\Enum\ChatMemberRankStatusEnum;
use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity, ORM\Table(name: 'choco_chat_member_rank')]
class ChatMemberRank
{
    #[ORM\Id, ORM\Column(type: 'string'), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    /**
     * @example 1 ранг (Младший модератор)
     * @example 2 ранг (Старший модератор)
     * @example 3 ранг (Младший администратор)
     * @example 4 ранг (Старший администратор)
     * @example 5 ранг (Создатель)
     */
    #[ORM\Column(type: 'smallint')]
    private int $rank = 0;

    public function __construct(
        #[ORM\OneToOne(mappedBy: 'rank', targetEntity: ChatMember::class)]
        private readonly ChatMember $member,
    ) {
        $this->id = UlidService::generate();
    }

    public function promote(int $rankIncrement): void
    {
        $newRank = ChatMemberRankStatusEnum::fromInteger(
            $this->rank + $rankIncrement
        );

        $this->rank = $newRank->value;
    }

    public function demote(int $rankDecrement): void
    {
        $newRank = ChatMemberRankStatusEnum::fromInteger(
            $this->rank - $rankDecrement
        );

        $this->rank = $newRank->value;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMember(): ChatMember
    {
        return $this->member;
    }

    public function getRankValue(): ChatMemberRankStatusEnum
    {
        return ChatMemberRankStatusEnum::fromInteger($this->rank);
    }

    public function getRankValuePrimitive(): int
    {
        return $this->rank;
    }

    public function setRankValue(ChatMemberRankStatusEnum $rank): void
    {
        $this->rank = $rank->value;
    }
}
