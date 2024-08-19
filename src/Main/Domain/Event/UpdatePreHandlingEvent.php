<?php

declare(strict_types=1);

namespace App\Main\Domain\Event;

use App\Main\Domain\Entity\Update\Update;
use App\Shared\Domain\Event\EventInterface;
use App\Shared\Domain\Event\StoppableEventInterface;

/**
 * This event means processing of the event itself before the start of
 * processing, updating of data about participants, chat and other data
 * requiring updating before the start of processing.
 * Also in case this update is not suitable for processing for some
 * reason it can be rejected by the listener.
 */
final class UpdatePreHandlingEvent
    implements EventInterface, StoppableEventInterface
{
    private bool $propagationStopped = false;

    public function __construct(private Update $update) {}

    public function getUpdate(): Update
    {
        return $this->update;
    }

    public function setUpdate(Update $update): void
    {
        $this->update = $update;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}
