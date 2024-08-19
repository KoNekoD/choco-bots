<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Main\Domain\Enum\UpdateHandleStatusEnum;
use App\Main\Domain\Event\UpdateHandleEvent;
use App\Shared\Domain\Entity\Aggregate;
use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity, ORM\Table(name: 'updates_update')]
class Update
    extends Aggregate
{
    final public const MAX_UPDATE_HANDLE_RETRIES = 5;

    #[ORM\Id, ORM\Column(type: 'string', length: 26), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    #[ORM\ManyToOne(targetEntity: UpdateChat::class)]
    #[ORM\JoinColumn(name: 'chat_id', referencedColumnName: 'id')]
    private ?UpdateChat $chat = null;

    #[ORM\ManyToOne(targetEntity: UpdateMessage::class)]
    #[ORM\JoinColumn(name: 'message_id', referencedColumnName: 'id')]
    private ?UpdateMessage $message = null;

    #[ORM\ManyToOne(targetEntity: CallbackQuery::class)]
    #[ORM\JoinColumn(name: 'callback_query_id', referencedColumnName: 'id')]
    private ?CallbackQuery $callbackQuery = null;

    #[ORM\Column(type: 'smallint', enumType: UpdateHandleStatusEnum::class)]
    private UpdateHandleStatusEnum $handleStatus;

    #[ORM\Column(type: 'smallint')]
    private int $handleRetriesCount;

    public function __construct(
        // @TODO If will be found problem with unique just remove this constraint
        #[ORM\Column(type: 'bigint', unique: true)]
        private readonly int $sourceUpdateId,
        #[ORM\Column(type: 'string')]
        private readonly string $sourceServiceName,
        #[ORM\Column(type: 'string')]
        private readonly string $botId
    ) {
        $this->id = UlidService::generate();

        $this->handleStatus = UpdateHandleStatusEnum::PENDING;
        $this->handleRetriesCount = 0;
    }

    public function tryHandleUpdate(): void
    {
        $this->handleStatus = UpdateHandleStatusEnum::IN_PROGRESS;
        $this->handleRetriesCount++;
        $this->raise(new UpdateHandleEvent($this->id));
    }

    public function handleFailed(): void
    {
        if ($this->handleRetriesCount >= self::MAX_UPDATE_HANDLE_RETRIES) { // Limit exceeded
            $this->handleStatus = UpdateHandleStatusEnum::REJECTED;
        } else {
            $this->handleStatus = UpdateHandleStatusEnum::FAILED;
        }
    }

    public function handleRejected(): void
    {
        $this->handleStatus = UpdateHandleStatusEnum::REJECTED;
    }

    public function handleFulfilled(): void
    {
        $this->handleStatus = UpdateHandleStatusEnum::FULFILLED;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSourceUpdateId(): int
    {
        return $this->sourceUpdateId;
    }

    public function getChat(): ?UpdateChat
    {
        return $this->chat;
    }

    public function setChat(?UpdateChat $chat): void
    {
        $this->chat = $chat;
    }

    public function getMessage(): ?UpdateMessage
    {
        return $this->message;
    }

    public function setMessage(?UpdateMessage $message): void
    {
        $this->message = $message;
    }

    public function getSourceServiceName(): string
    {
        return $this->sourceServiceName;
    }

    public function getBotId(): string
    {
        return $this->botId;
    }

    public function getHandleStatus(): UpdateHandleStatusEnum
    {
        return $this->handleStatus;
    }

    public function getHandleRetriesCount(): int
    {
        return $this->handleRetriesCount;
    }

    public function getCallbackQuery(): ?CallbackQuery
    {
        return $this->callbackQuery;
    }

    public function setCallbackQuery(?CallbackQuery $callbackQuery): void
    {
        $this->callbackQuery = $callbackQuery;
    }

    public function isCallbackQuery(): bool
    {
        return null !== $this->callbackQuery;
    }

}
