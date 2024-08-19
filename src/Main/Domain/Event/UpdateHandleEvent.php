<?php

declare(strict_types=1);

namespace App\Main\Domain\Event;

use App\Shared\Domain\Event\EventInterface;

final readonly class UpdateHandleEvent
    implements EventInterface
{
    public function __construct(public string $updateId) {}
}
