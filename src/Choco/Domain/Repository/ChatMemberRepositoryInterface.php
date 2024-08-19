<?php

declare(strict_types=1);

namespace App\Choco\Domain\Repository;

use App\Choco\Domain\Entity\Choco\ChatMember;
use App\Choco\Domain\Entity\Choco\ChatMemberRank;
use App\Choco\Domain\Exception\ChocoEntity\ChatMemberException;
use App\Main\Domain\Entity\Update\Update;

interface ChatMemberRepositoryInterface
{
    public function add(ChatMember $member, bool $flush = false): void;

    public function addRank(ChatMemberRank $rank, bool $flush = false): void;

    public function save(): void;

    /** @throws ChatMemberException */
    public function findChatMemberByFirstMentionOrUsername(
        Update $update,
        string $username
    ): ChatMember;

    /**
     * @param string[] $usernameList
     *
     * @return ChatMember[]
     */
    public function findChatMembersByMentionOrUsername(
        Update $update,
        array $usernameList
    ): array;

    /** @throws ChatMemberException */
    public function findChatMemberById(string $id): ChatMember;

    /** @throws ChatMemberException */
    public function findChatMember(
        int $sourceChatId,
        int $sourceUserId,
        string $sourceServiceName
    ): ChatMember;

    /** @return ChatMember[] */
    public function getChatMembersWithPrivileges(
        int $sourceChatId,
        string $sourceServiceName
    ): array;
}
