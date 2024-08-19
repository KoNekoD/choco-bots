<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Domain\Event\EventInterface;
use App\Shared\Domain\Event\StoppableEventInterface;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class EventBus
    implements EventBusInterface
{
    public function __construct(private MessageBusInterface $eventBus) {}

    public function execute(EventInterface $event): mixed
    {
        $envelope = $this->eventBus->dispatch($event);
        /** @var HandledStamp[] $handledStamps */
        $handledStamps = $envelope->all(HandledStamp::class);

        if ($handledStamps === []) {
            return null; // Async
        }

        if (count($handledStamps) > 1) {
            $handlers = implode(
                ', ',
                array_map(static function (
                    HandledStamp $stamp
                ): string {
                    return sprintf('"%s"', $stamp->getHandlerName());
                }, $handledStamps)
            );

            if (!$event instanceof StoppableEventInterface) {
                // Multiple handlers problem
                throw new LogicException(
                    sprintf(
                        'UpdateMessage of type "%s" was handled multiple times. Only one handler is expected when using "%s::%s()", got %d: %s.',
                        get_debug_type($envelope->getMessage()),
                        self::class,
                        __FUNCTION__,
                        count($handledStamps),
                        $handlers
                    )
                );
            }
        }

        return $handledStamps[0]->getResult(); // Sync
    }
}
