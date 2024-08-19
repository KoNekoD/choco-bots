<?php

declare(strict_types=1);

namespace App\Choco\Domain\ChatClientAPI;

use App\Choco\Domain\Entity\Choco\ChatMember;
use App\Choco\Domain\Entity\Choco\ChocoUser;
use App\Main\Domain\ChatClientAPI\ChatClientInterface;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Main\Domain\Entity\Update\UpdateMessage;
use App\Main\Domain\Entity\Update\UpdateUser;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;

interface ChocoChatClientInterface
    extends ChatClientInterface
{
    /**
     * Implementations MUST return persisted entity.
     *
     * @throws ChatClientAPIException
     */
    public function getChatMember(int $chatId, int $userId): ChatMember;

    public function getChatMemberMentionString(
        string $mentionName,
        UpdateUser $user
    ): string;

    /** @deprecated */
    public function getChatMemberLinkString(ChocoUser $user): string;

    /** @throws ChatClientAPIException */
    public function trimUsername(string $username): string;

    public function deleteMessage(UpdateMessage $message): ChatClientResultDTO;

    public function muteChatMember(
        ChatMember $target,
        ?string $muteReason
    ): ChatClientResultDTO;
}
