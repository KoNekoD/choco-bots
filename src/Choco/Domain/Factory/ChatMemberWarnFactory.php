<?php

declare(strict_types=1);

namespace App\Choco\Domain\Factory;

use App\Choco\Domain\Entity\Choco\ChatMember;
use App\Choco\Domain\Entity\Choco\ChatMemberWarn;
use App\Choco\Domain\Repository\ChatMemberWarnRepositoryInterface;
use DateTimeImmutable;

final readonly class ChatMemberWarnFactory
{
    public function __construct(
        private ChatMemberWarnRepositoryInterface $chocoRepository
    ) {}

    public function createChatMemberWarn(
        ChatMember $warned,
        ChatMember $creator,
        string $reason,
        DateTimeImmutable $expiresAt
    ): ChatMemberWarn {
        $entity = new ChatMemberWarn(
            $warned,
            $creator,
            $creator->getChat(),
            $reason,
            $expiresAt
        );
        $this->chocoRepository->add($entity);

        // @TODO Add warns count checking.
        // If count exceed default value -> chat member need to be punished

        return $entity;
    }
}
