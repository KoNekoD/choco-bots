<?php

declare(strict_types=1);

namespace App\Choco\Domain\Event\ChatMember;

use App\Shared\Domain\Event\EventInterface;

final readonly class NewMemberNotifyEvent
    implements EventInterface
{
    public function __construct(public string $chatMemberId) {}
}
