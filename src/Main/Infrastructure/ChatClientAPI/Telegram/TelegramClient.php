<?php

declare(strict_types=1);

namespace App\Main\Infrastructure\ChatClientAPI\Telegram;

use App\Main\Domain\ChatClientAPI\ChatClientInterface;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;
use App\Main\Domain\Exception\ChatClientAPI\GetUpdatesException;
use App\Main\Domain\Repository\UpdateRepositoryInterface;
use App\Main\Infrastructure\Factory\UpdateFactory;
use BoShurik\TelegramBotBundle\Telegram\BotLocator;
use Exception;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\ForceReply;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardRemove;
use function Symfony\Component\String\u;

abstract class TelegramClient
    implements ChatClientInterface
{
    protected BotApi $bot;

    public function __construct(
        BotLocator $botLocator,
        private readonly UpdateRepositoryInterface $updateRepository,
        private readonly UpdateFactory $updateFactory,
    ) {
        $this->bot = $botLocator->get(static::getBotId());
    }

    abstract public static function getBotId(): string;

    public function pullUpdates(?int $lastSavedUpdateId = null): array
    {
        try {
            $updateEntityList = [];

            if (null === $lastSavedUpdateId) {
                $lastUpdateId = $this->updateRepository->getLastUpdateId(
                    self::getClientAdapterName()
                );
            } else {
                $lastUpdateId = $lastSavedUpdateId;
            }

            $offset = 0;
            if (0 !== $lastUpdateId) {
                // Next message will be first in result array
                $offset = $lastUpdateId + 1;
            }

            $updates = $this->bot->getUpdates($offset);
            foreach ($updates as $update) {
                $updateEntity = $this
                    ->updateFactory
                    ->createUpdateFromTelegram(
                        $update,
                        static::getBotId()
                    );

                if (null !== $updateEntity) {
                    $updateEntityList[] = $updateEntity;
                }
            }

            return $updateEntityList;
        } catch (Exception $e) {
            throw new GetUpdatesException($e->getMessage());
        }
    }

    public static function getClientAdapterName(): string
    {
        return 'Telegram';
    }

    public function sendMessage(
        int|string $chatId,
        string $text,
        ?string $parseMode = 'HTML',
        bool $disablePreview = false,
        ?bool $protectContent = null,
        bool $disableNotification = false,
        ?int $replyToMessageId = null,
        ?bool $allowSendingWithoutReply = null,
        InlineKeyboardMarkup|ReplyKeyboardMarkup|ReplyKeyboardRemove|ForceReply|null $replyMarkup = null,
    ): ChatClientResultDTO {
        $lim = 4096;
        $textNext = null;
        if (strlen($text) > $lim) {
            $textNext = u($text)->slice($lim)->toString();
            $text = u($text)->slice(0, $lim)->toString();
        }
        try {
            $this->bot->sendMessage(
                chatId: $chatId,
                text: $text,
                parseMode: $parseMode,
                disablePreview: $disablePreview,
                replyToMessageId: $replyToMessageId,
                disableNotification: $disableNotification,
            );

            if (null !== $textNext) {
                $this->sendMessage(
                    $chatId,
                    $textNext,
                    $parseMode,
                    $disablePreview,
                    $protectContent,
                    $disableNotification,
                    $replyToMessageId,
                    $allowSendingWithoutReply,
                    $replyMarkup,
                );
            }

            return new ChatClientResultDTO(true);
        } catch (\TelegramBot\Api\Exception $e) {
            return new ChatClientResultDTO(false, error: $e->getMessage());
        }
    }

    /** @throws ChatClientAPIException */
    public function trimUsername(string $username): string
    {
        $spacePosition = strpos($username, ' ');
        if (false !== $spacePosition) {
            $username = substr($username, 0, $spacePosition);
        }

        $b1 = !str_contains($username, '@');
        $b2 = !str_contains($username, 't.me');

        if ($b1 && $b2) {
            throw new ChatClientAPIException('Incorrect username');
        }

        return str_replace(
            '@',
            '',
            str_replace(
                '/',
                '',
                str_replace(
                    't.me',
                    '',
                    str_replace(
                        'https://',
                        '',
                        trim($username)
                    )
                )
            )
        );
    }
}
