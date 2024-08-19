<?php

declare(strict_types=1);

namespace App\Main\Infrastructure\Integrations;

use App\Main\Application\Service\SyncUpdateHandleService;
use App\Main\Domain\Exception\BaseException;
use App\Main\Infrastructure\Factory\UpdateFactory;
use BoShurik\TelegramBotBundle\Event\WebhookEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final readonly class TelegramWebhookEventListener
{
    public function __construct(
        private UpdateFactory $updateFactory,
        private SyncUpdateHandleService $syncUpdateHandleService,
        private LoggerInterface $logger
    ) {}

    public function __invoke(WebhookEvent $event): void
    {
        try {
            $update = $this->updateFactory->createUpdateFromTelegram(
                $event->getUpdate(),
                $event->getBot()
            );

            if ($update !== null) {
                $this->syncUpdateHandleService->handle($update);
            }
        } catch (BaseException $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
