<?php

declare(strict_types=1);

namespace App\Choco\Domain\Event\Marry;

use App\Shared\Domain\Event\EventInterface;

final readonly class MarryRequestEvent
    implements EventInterface
{
    /** @param string[] $participantUserIds */
    public function __construct(
        public array $participantUserIds,
        public string $creatorChatMemberId,
    ) {}
}
