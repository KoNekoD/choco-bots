<?php

declare(strict_types=1);

namespace App\Choco\Domain\Repository;

use App\Choco\Domain\DTO\ChatMemberMessagesStatDTO;
use App\Choco\Domain\Entity\Choco\ChatMember;
use App\Choco\Domain\Entity\Choco\ChocoChat;
use App\Choco\Domain\Entity\Choco\ChocoUser;
use App\Choco\Domain\Entity\Choco\Marry;
use App\Choco\Domain\Exception\ChocoEntity\ChocoChatNotFoundException;
use App\Choco\Domain\Exception\ChocoEntity\UserNotFoundException;
use App\Main\Domain\Entity\Update\Update;
use App\Main\Domain\Entity\Update\UpdateChat;
use DateTimeImmutable;

interface ChocoRepositoryInterface
{
    public function add(object $entity, bool $flush = false): void;

    public function findById(string $id): ?Update;

    public function save(): void;

    public function remove(object $entity): void;

    /** @throws ChocoChatNotFoundException */
    public function findChatBySourceChatId(
        int $sourceChatId,
        string $sourceServiceName
    ): ChocoChat;

    /** @throws UserNotFoundException */
    public function findUserBySourceUserId(
        int $sourceUserId,
        string $sourceServiceName
    ): ChocoUser;

    /** @throws UserNotFoundException */
    public function findUserById(string $id): ChocoUser;

    public function getMessagesCountAggregatedByChatMemberAndTimeRange(
        DateTimeImmutable $fromDate,
        DateTimeImmutable $toDate,
        ChatMember $who
    ): int;

    /** @return ChatMemberMessagesStatDTO[] */
    public function getMessagesStatsAggregatedByChatAndTimeRange(
        DateTimeImmutable $fromDate,
        DateTimeImmutable $toDate,
        ChocoChat $chat
    ): array;

    /** @return Marry[] */
    public function getMarriesByChat(
        ChocoChat $chat
    ): array;

    /** @throws ChocoChatNotFoundException */
    public function getChocoChatByUpdateChat(
        UpdateChat $updateChat
    ): ChocoChat;
}
