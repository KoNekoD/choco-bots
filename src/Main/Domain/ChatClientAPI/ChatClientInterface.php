<?php

declare(strict_types=1);

namespace App\Main\Domain\ChatClientAPI;

use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Main\Domain\Entity\Update\Update;
use App\Main\Domain\Exception\ChatClientAPI\GetUpdatesException;
use TelegramBot\Api\Types\ForceReply;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardRemove;

interface ChatClientInterface
{
    public static function getClientAdapterName(): string;

    public static function getBotId(): string;

    /**
     * Implementations MUST return persisted array of entities.
     *
     * @return Update[]
     *
     * @throws GetUpdatesException
     */
    public function pullUpdates(?int $lastSavedUpdateId = null): array;

    public function sendMessage(
        int|string $chatId,
        string $text,
        ?string $parseMode = null,
        bool $disablePreview = false,
        ?bool $protectContent = null,
        bool $disableNotification = false,
        ?int $replyToMessageId = null,
        ?bool $allowSendingWithoutReply = null,
        InlineKeyboardMarkup|ReplyKeyboardMarkup|ReplyKeyboardRemove|ForceReply|null $replyMarkup = null
    ): ChatClientResultDTO;
}
