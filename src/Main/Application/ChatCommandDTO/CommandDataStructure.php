<?php

declare(strict_types=1);

namespace App\Main\Application\ChatCommandDTO;

use App\Main\Domain\Entity\Update\Update;
use App\Main\Domain\Entity\Update\UpdateChat;
use App\Main\Domain\Entity\Update\UpdateMessage;
use App\Main\Domain\Entity\Update\UpdateUser;
use DomainException;

final readonly class CommandDataStructure
{
    public function __construct(private Update $update) {}

    public function getUpdate(): Update
    {
        return $this->update;
    }

    public function getUpdateMessageReplyToMessage(): UpdateMessage
    {
        $reply = $this->getUpdateMessage()->getReplyToMessage();

        if (null === $reply) {
            throw new DomainException(
                sprintf(
                    'UpdateMessage->ReplyToMessage is not set in this update %s',
                    $this->update->getId()
                )
            );
        }

        return $reply;
    }

    public function getUpdateMessage(): UpdateMessage
    {
        $message = $this->update->getMessage();

        if (null === $message) {
            throw new DomainException(
                sprintf(
                    'Message is not set but previous check must skip this update %s',
                    $this->update->getId()
                )
            );
        }

        return $message;
    }

    public function getUpdateMessageFrom(): UpdateUser
    {
        $from = $this->getUpdateMessage()->getFrom();

        if (null === $from) {
            throw new DomainException(
                sprintf(
                    'UpdateMessage->From is not set in this update %s',
                    $this->update->getId()
                )
            );
        }

        return $from;
    }

    public function getUpdateChatSourceChatId(): int
    {
        return $this->getUpdateChat()->getSourceChatId();
    }

    public function getUpdateChat(): UpdateChat
    {
        $chat = $this->update->getChat();

        if (null === $chat) {
            throw new DomainException(
                sprintf(
                    'Chat is not set in this update %s',
                    $this->update->getId()
                )
            );
        }

        return $chat;
    }
}
