<?php

declare(strict_types=1);

namespace App\Main\Domain\Repository;

use App\Main\Domain\DTO\UpdateSourceDTO;
use App\Main\Domain\Entity\Update\Update;
use App\Main\Domain\Entity\Update\UpdateChat;
use App\Main\Domain\Entity\Update\UpdateMessage;
use App\Main\Domain\Entity\Update\UpdateUser;
use App\Main\Domain\Exception\UpdateEntities\UpdateChatNotFoundException;
use App\Main\Domain\Exception\UpdateEntities\UpdateMessageNotFoundException;
use App\Main\Domain\Exception\UpdateEntities\UpdateNotFoundException;
use App\Main\Domain\Exception\UpdateEntities\UpdateUserNotFoundException;

interface UpdateRepositoryInterface
{
    public function add(object $entity, bool $flush = false): void;

    public function freshFlush(): void;

    /** @throws UpdateNotFoundException */
    public function findUpdateById(string $id): Update;

    public function isExistUpdateBySourceUpdateId(
        int $updateId,
        string $sourceServiceName,
        string $botId
    ): bool;

    public function getLastUpdateId(string $sourceServiceName): int;

    public function save(): void;

    /** @return Update[] */
    public function getUpdatesForHandle(): array;

    /** @throws UpdateChatNotFoundException */
    public function getChatBySourceDTO(UpdateSourceDTO $DTO): UpdateChat;

    /** @throws UpdateMessageNotFoundException */
    public function getMessageBySourceDTO(UpdateSourceDTO $DTO): UpdateMessage;

    /** @throws UpdateUserNotFoundException */
    public function getUserBySourceDTO(UpdateSourceDTO $DTO): UpdateUser;

    public function isExistCallbackQueryByTelegramId(string $id): bool;
}
