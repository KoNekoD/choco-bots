<?php

declare(strict_types=1);

namespace App\Main\Application\EventHandler;

use App\Main\Application\ChatCommandContracts\AbstractChatCommand;
use App\Main\Application\ChatCommandDTO\CommandDataStructure;
use App\Main\Application\Event\UpdateSearchCommandStartedEvent;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Main\Domain\Event\UpdateHandleEvent;
use App\Main\Domain\Event\UpdatePreHandlingEvent;
use App\Main\Domain\Exception\BaseException;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;
use App\Main\Domain\Exception\CommandNotReturnedResultException;
use App\Main\Domain\Exception\NoSuitableCommandFoundException;
use App\Main\Domain\Exception\UpdateEntities\UpdateNotFoundException;
use App\Main\Domain\Repository\UpdateRepositoryInterface;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Domain\Event\EventHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * @todo Now it sync event and not require em.flush to guarantee sync save data and dispatch_aggregate_events
 */
final readonly class UpdateHandleEventHandler
    implements EventHandlerInterface
{
    public function __construct(
        private UpdateRepositoryInterface $updateRepository,
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger,
        private EventBusInterface $eventBus,
    ) {}

    /**
     * @throws ChatClientAPIException
     * @throws BaseException
     */
    public function __invoke(UpdateHandleEvent $event): void
    {
        try {
            $update = $this->updateRepository->findUpdateById($event->updateId);

            $updateMessage = $update->getMessage();

            if (null === $updateMessage) {
                $update->handleRejected();
                $this->updateRepository->save();
                $this->logger->warning(
                    "Update $event->updateId does not have message field"
                );

                return;
            }

            $this->eventBus->execute(new UpdatePreHandlingEvent($update));

            // Command checking section
            $updateMessageText = $updateMessage->getText();
            if (null === $updateMessageText) {
                $update->handleRejected();
                $this->updateRepository->save();

                return;
            }

            try {
                $searchCommandEvent = new UpdateSearchCommandStartedEvent(
                    $update
                );
                $this->eventBus->execute($searchCommandEvent);
                $command = $searchCommandEvent->getCommand();

                if (!$command instanceof AbstractChatCommand) {
                    $update->handleRejected();
                    $this->updateRepository->save();

                    return;
                }

                if (null === $updateMessage->getFrom()) {
                    $update->handleRejected(); // If sender unknown
                    $this->updateRepository->save();

                    return;
                }

                $dataStructure = new CommandDataStructure($update);

                $command->loadInitialConfiguration($dataStructure);

                /** @var ?ChatClientResultDTO $result */
                $result = $this->commandBus->execute($command);

                if (null === $result) {// @TODO Refactor thisB
                    throw new CommandNotReturnedResultException(
                        'ChatCommand with name '.$command::class.' does not returned ChatClientResultDTO'
                    );
                }

                if ($result->isOk) {
                    $update->handleFulfilled();
                } elseif ($result->isFinallyFailed) {
                    $this->logger->warning(
                        'Update finally failed',
                        [
                            'reason' => $result->getError(),
                            'updateId' => $event->updateId,
                        ]
                    );
                    $update->handleRejected();
                } else {
                    $this->logger->warning(
                        'Update failed',
                        [
                            'reason' => $result->getError(),
                            'updateId' => $event->updateId,
                        ]
                    );
                    $update->handleFailed();
                }
            } catch (NoSuitableCommandFoundException) {
                $update->handleRejected(); // @TODO refactor
            }
            $this->updateRepository->save();
        } catch (UpdateNotFoundException $searchCommandEvent) {
            $this->logger->warning(
                "error: {$searchCommandEvent->getMessage()}, updateId: $event->updateId"
            );
        } catch (Throwable $exception) {
            throw new BaseException(
                "Unhandled exception. Update: $event->updateId Error: {$exception->getMessage()}",
                $exception->getCode(),
                $exception
            );
        }
    }
}
