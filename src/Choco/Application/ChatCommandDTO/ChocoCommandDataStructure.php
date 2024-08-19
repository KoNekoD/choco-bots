<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommandDTO;

use App\Choco\Domain\ChatClientAPI\ChocoChatClientInterface;
use App\Choco\Domain\Entity\Choco\ChatMember;
use App\Choco\Domain\Entity\Choco\ChocoChat;
use App\Main\Domain\Entity\Update\UpdateChat;
use App\Main\Domain\Entity\Update\UpdateMessage;
use App\Main\Domain\Entity\Update\UpdateUser;
use DomainException;

final readonly class ChocoCommandDataStructure
{
    /**
     * @param ChatMember $who Means the person who sent this message -
     *                        its sender
     */
    public function __construct(
        public ChocoChat $chat,
        public ChocoChatClientInterface $client,
        public ChatMember $who,
        public ?UpdateUser $replyFrom,
        public ?UpdateMessage $reply
    ) {}

    public function getReplyFrom(): UpdateUser
    {
        if (null === $this->replyFrom) {
            throw new DomainException(
                'Reply from is not set in ChocoCommandDataStructure'
            );
        }

        return $this->replyFrom;
    }

    public function getUpdateChat(): UpdateChat
    {
        return $this->chat->getUpdateChat();
    }

    public function getUpdateMessageReplyToMessageOrNull(): ?UpdateMessage
    {
        return $this->reply;
    }
}
