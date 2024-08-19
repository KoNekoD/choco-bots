<?php

declare(strict_types=1);

namespace App\Choco\Domain\Repository;

use App\Choco\Domain\Entity\Choco\ChatMemberWarn;

interface ChatMemberWarnRepositoryInterface
{
    public function add(ChatMemberWarn $warn, bool $flush = false): void;
}
