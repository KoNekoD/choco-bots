<?php

declare(strict_types=1);

namespace App\Main\Infrastructure\Service;

use App\Main\Domain\Exception\ChatClientAPI\GetUpdatesException;
use App\Main\Domain\Repository\UpdateRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\LockFactory;

final readonly class UpdateProviderService
{
    public function __construct(
        private UpdateRepositoryInterface $updateRepository,
        private ChatClientApiProviderFactory $apiProviderFactory,
        private LoggerInterface $logger,
        private LockFactory $lockFactory,
    ) {}

    public function pullUpdates(SymfonyStyle $io): bool
    {
        $lock = $this->lockFactory->createLock(
            'update_provider_pulling_updates',
            600
        );

        if (!$lock->acquire()) {
            $this->logger->notice(
                'pullUpdates: This method is running in another process.'
            );

            return false;
        }

        $prefix = 'UpdateProviderService::pullUpdates:';

        $apiList = $this->apiProviderFactory->getApiClientList();
        foreach ($apiList as $api) {
            try {
                $this->logger->info(
                    "$prefix Using {$api::getClientAdapterName()} service"
                );

                $currentPulledUpdates = $api->pullUpdates();
                while (!empty($currentPulledUpdates)) {
                    $countUpdates = count($currentPulledUpdates);

                    foreach ($currentPulledUpdates as $update) {
                        $io->text(
                            sprintf(
                                'Got update %s. Bot: %s, Text: %s',
                                $update->getId(),
                                $update->getBotId(),
                                $update->getMessage()?->getText(
                                ) ?? '_NO_MESSAGE'
                            )
                        );
                    }

                    $this->logger->info(
                        "$prefix Got $countUpdates updates. Saving"
                    );
                    $this->updateRepository->freshFlush();

                    $currentPulledUpdates = $api->pullUpdates(
                        $currentPulledUpdates[count(
                            $currentPulledUpdates
                        ) - 1 // Last element
                        ]->getSourceUpdateId(),
                    );
                }

                $this->logger->info("$prefix Done");
            } catch (GetUpdatesException $e) {
                $this->logger->warning($e->getMessage());
            }
        }

        $lock->release();

        return true;
    }

    public function handleUpdates(): void
    {
        $lock = $this->lockFactory->createLock(
            'update_provider_handling_updates',
            600
        );

        if (!$lock->acquire()) {
            $this->logger->notice(
                'handleUpdates: This method is running in another process.'
            );

            return;
        }

        $prefix = 'UpdateProviderService::handleUpdates:';

        $updates = $this->updateRepository->getUpdatesForHandle();

        $countUpdates = count($updates);

        $this->logger->info(
            "$prefix Got $countUpdates updates for handle"
        );

        foreach ($updates as $update) {
            $update->tryHandleUpdate();
        }

        $this->logger->info("$prefix Saving...");

        $this->updateRepository->save();

        $lock->release();
    }
}
