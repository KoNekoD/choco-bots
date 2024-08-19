<?php

declare(strict_types=1);

namespace App\Choco\Domain\DTO;

use App\Choco\Domain\Entity\Choco\ChocoUser;

final readonly class ChatMemberMessagesStatDTO
{
    public function __construct(
        public ChocoUser $user,
        public int $messagesCount
    ) {}
}
