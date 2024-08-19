<?php

declare(strict_types=1);

namespace App\Choco\Infrastructure\ChatClientAPI;

use App\Choco\Domain\ChatClientAPI\ChocoChatClientInterface;
use App\Choco\Domain\Entity\Choco\ChatMember;
use App\Choco\Domain\Entity\Choco\ChocoUser;
use App\Choco\Domain\Exception\ChocoEntity\ChatMemberException;
use App\Choco\Infrastructure\Factory\ChatMemberFactory;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Main\Domain\Entity\Update\UpdateMessage;
use App\Main\Domain\Entity\Update\UpdateUser;
use App\Main\Domain\Exception\BaseException;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;
use App\Main\Domain\Repository\UpdateRepositoryInterface;
use App\Main\Infrastructure\ChatClientAPI\Telegram\TelegramClient;
use App\Main\Infrastructure\Factory\UpdateFactory;
use BoShurik\TelegramBotBundle\Telegram\BotLocator;
use DateTimeImmutable;
use TelegramBot\Api\Exception;

final class ChocoTelegramClient
    extends TelegramClient
    implements ChocoChatClientInterface
{
    public function __construct(
        BotLocator $botLocator,
        UpdateRepositoryInterface $updateRepository,
        UpdateFactory $updateFactory,
        private readonly ChatMemberFactory $chatMemberFactory,
    ) {
        parent::__construct(
            $botLocator,
            $updateRepository,
            $updateFactory
        );
        $this->bot = $botLocator->get(self::getBotId());
    }

    public static function getBotId(): string
    {
        return 'cock5';
    }

    public function getChatMember(int $chatId, int $userId): ChatMember
    {
        try {
            $member = $this->bot->getChatMember(
                $chatId,
                $userId
            );

            return $this->chatMemberFactory->createOrGetAndUpdateChatMemberFromTelegramResponse(
                $member,
                $chatId
            );
        } catch (ChatMemberException|BaseException|Exception $e) {
            throw new ChatClientAPIException($e->getMessage());
        }
    }

    public function muteChatMember(
        ChatMember $target,
        ?string $muteReason
    ): ChatClientResultDTO {
        $untilDate = (
            (new DateTimeImmutable())->getTimestamp()
            +
            $target->getChat()->getDefaultMuteTimeInSeconds()
        );
        try {
            $this->bot->restrictChatMember(
                chatId: $target->getChat()->getSourceChatId(),
                userId: $target->getUser()->getSourceUserId(),
                untilDate: $untilDate,
            );

            return new ChatClientResultDTO(true);
        } catch (Exception $e) {
            return ChatClientResultDTO::fail($e->getMessage());
        }
    }

    public function getChatMemberMentionString(
        string $mentionName,
        UpdateUser $user
    ): string {
        return
            sprintf(
                '<a href="tg://user?id=%s">%s</a>',
                $user->getSourceUserId(),
                $mentionName
            );
        // return "[$mentionName](tg://user?id={$user->getSourceUserId()})";
    }

    public function deleteMessage(UpdateMessage $message): ChatClientResultDTO
    {
        try {
            $this->bot->deleteMessage(
                chatId: $message->getChatSourceChatId(),
                messageId: $message->getSourceMessageId()
            );

            return new ChatClientResultDTO(true);
        } catch (Exception $e) {
            return ChatClientResultDTO::fail($e->getMessage());
        }
    }

    public function getChatMemberLinkString(ChocoUser $user): string
    {
        if ($username = $user->getUpdateUsername()) {
            return "https://t.me/$username";
        }

        return '-No-Link-';
    }
}
