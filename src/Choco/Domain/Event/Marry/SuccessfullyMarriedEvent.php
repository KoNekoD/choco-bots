<?php

declare(strict_types=1);

namespace App\Choco\Domain\Event\Marry;

use App\Shared\Domain\Event\EventInterface;

final readonly class SuccessfullyMarriedEvent
    implements EventInterface
{
    /** @param string[] $participantIds */
    public function __construct(
        public array $participantIds,
        public int|string $sourceChatId
    ) {}
}
