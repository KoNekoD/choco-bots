<?php

declare(strict_types=1);

namespace App\Shared\Domain\Event;

use Psr\EventDispatcher\StoppableEventInterface as BaseStoppableEventInterface;

interface StoppableEventInterface
    extends BaseStoppableEventInterface
{
}
