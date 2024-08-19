<?php

declare(strict_types=1);

namespace App\Main\Application\Event;

use App\Main\Application\ChatCommandContracts\AbstractChatCommand;
use App\Main\Domain\Entity\Update\Update;
use App\Main\Domain\Entity\Update\UpdateChat;
use App\Main\Domain\Entity\Update\UpdateMessage;
use App\Main\Domain\Entity\Update\UpdateUser;
use App\Main\Domain\Exception\NoSuitableCommandFoundException;
use App\Shared\Domain\Event\EventInterface;
use App\Shared\Domain\Event\StoppableEventInterface;
use DomainException;

/**
 * This event indicates a search for the right command for this Update
 */
final class UpdateSearchCommandStartedEvent
    implements EventInterface, StoppableEventInterface
{
    private ?AbstractChatCommand $command = null;

    private bool $propagationStopped = false;

    public function __construct(private readonly Update $update) {}

    /** @throws NoSuitableCommandFoundException */
    public function getCommand(): ?AbstractChatCommand
    {
        if ($this->command === null) {
            throw new NoSuitableCommandFoundException(
                'This update does not have suitable command'
            );
        }

        return $this->command;
    }

    public function setCommand(?AbstractChatCommand $command): void
    {
        $this->command = $command;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    public function getSourceServiceName(): string
    {
        return $this->update->getSourceServiceName();
    }

    public function getBotId(): string
    {
        return $this->update->getBotId();
    }

    public function isCallbackQuery(): bool
    {
        return $this->update->isCallbackQuery();
    }

    public function getTextData(): string
    {
        if ($this->update->isCallbackQuery()) {
            $callbackQuery = $this->update->getCallbackQuery();

            if (null === $callbackQuery) {
                throw new DomainException(
                    "CallbackQuery is not set but previous check must skip this update {$this->update->getId()}"
                );
            }

            $data = $callbackQuery->getData();

            if (null === $data) {
                throw new DomainException(
                    "Data is not set but previous check must skip this update {$this->update->getId()}"
                );
            }

            return $data;
        }

        $text = $this->getUpdateMessage()->getText();

        if (null === $text) {
            throw new DomainException(
                "Text is not set but previous check must skip this update {$this->update->getId()}"
            );
        }

        return $text;
    }

    public function getUpdateMessage(): UpdateMessage
    {
        $message = $this->update->getMessage();

        if (null === $message) {
            throw new DomainException(
                "Message is not set but previous check must skip this update {$this->update->getId()}"
            );
        }

        return $message;
    }

    public function getSourceChatId(): int
    {
        return $this->getUpdateChat()->getSourceChatId();
    }

    public function getUpdateChat(): UpdateChat
    {
        $chat = $this->update->getChat();

        if (null === $chat) {
            throw new DomainException(
                "Chat is not set in update {$this->update->getId()}"
            );
        }

        return $chat;
    }

    public function getSourceMessageFromSourceId(): int
    {
        return $this->getUpdateMessageFrom()->getSourceUserId();
    }

    public function getUpdateMessageFrom(): UpdateUser
    {
        $from = $this->getUpdateMessage()->getFrom();

        if (null === $from) {
            throw new DomainException(
                "Message->From is not set in update {$this->update->getId()}"
            );
        }

        return $from;
    }

    public function getUpdateMessageReplyToMessage(): ?UpdateMessage
    {
        return $this->getUpdateMessage()->getReplyToMessage();
    }

    public function getUpdateMessageReplyToMessageFrom(): ?UpdateUser
    {
        return $this->getUpdateMessage()->getReplyToMessage()?->getFrom();
    }
}
