<?php

declare(strict_types=1);

namespace App\Main\Application\Service;

use App\Main\Application\ChatCommandContracts\AbstractChatCommand;
use App\Main\Application\ChatCommandDTO\CommandDataStructure;
use App\Main\Application\Event\UpdateSearchCommandStartedEvent;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Main\Domain\Entity\Update\Update;
use App\Main\Domain\Event\UpdatePreHandlingEvent;
use App\Main\Domain\Exception\BaseException;
use App\Main\Domain\Exception\CommandNotReturnedResultException;
use App\Main\Domain\Exception\NoSuitableCommandFoundException;
use App\Main\Domain\Repository\UpdateRepositoryInterface;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Event\EventBusInterface;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Can be used if update is processed via webhook
 */
final readonly class SyncUpdateHandleService
{
    public function __construct(
        private UpdateRepositoryInterface $updateRepository,
        private CommandBusInterface $commandBus,
        private LoggerInterface $syncUpdateHandleService,
        private EventBusInterface $eventBus,
    ) {}

    /**
     * @throws BaseException
     */
    public function handle(Update $update): void
    {
        try {
            $updateMessage = $update->getMessage();

            if (null === $updateMessage) {
                $update->handleRejected();
                $this->updateRepository->save();
                $this->syncUpdateHandleService->warning(
                    "Update {$update->getId()} does not have message field"
                );

                return;
            }

            $this->eventBus->execute(
                new UpdatePreHandlingEvent($update)
            );

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

                $command->loadInitialConfiguration(
                    $dataStructure
                );

                /** @var ?ChatClientResultDTO $result */
                $result = $this->commandBus->execute($command);

                if (null === $result) {// @TODO Refactor thisB
                    throw new CommandNotReturnedResultException(
                        sprintf(
                            'ChatCommand with name %s does not returned ChatClientResultDTO',
                            $command::class
                        )
                    );
                }

                if ($result->isOk) {
                    $update->handleFulfilled();
                } elseif ($result->isFinallyFailed) {
                    $this->syncUpdateHandleService->warning(
                        'Update finally failed',
                        [
                            'reason' => $result->getError(),
                            'updateId' => $update->getId(),
                        ]
                    );
                    $update->handleRejected();
                } else {
                    $this->syncUpdateHandleService->warning(
                        'Update failed',
                        [
                            'reason' => $result->getError(),
                            'updateId' => $update->getId(),
                        ]
                    );
                    $update->handleFailed();
                }
            } catch (NoSuitableCommandFoundException) {
                $update->handleRejected(); // @TODO refactor
            }
            $this->updateRepository->save();
        } catch (Throwable $exception) {
            throw new BaseException(
                sprintf(
                    'Unhandled exception. Update: %s Error: %s',
                    $update->getId(),
                    $exception->getMessage()
                ),
                $exception->getCode(),
                $exception
            );
        }
    }
}
