<?php

declare(strict_types=1);

namespace App\Choco\Infrastructure\Factory;

use App\Choco\Domain\Entity\Choco\ChocoChat;
use App\Choco\Domain\Entity\Choco\ChocoUser;
use App\Choco\Domain\Exception\ChocoEntity\ChocoChatNotFoundException;
use App\Choco\Domain\Exception\ChocoEntity\UserNotFoundException;
use App\Choco\Domain\Repository\ChocoRepositoryInterface;
use App\Main\Domain\DTO\UpdateSourceDTO;
use App\Main\Domain\Exception\UpdateEntities\UpdateChatNotFoundException;
use App\Main\Domain\Exception\UpdateEntities\UpdateUserNotFoundException;
use App\Main\Domain\Repository\UpdateRepositoryInterface;

final readonly class ChocoFactory
{
    public function __construct(
        private ChocoRepositoryInterface $chocoRepository,
        private UpdateRepositoryInterface $updateRepository,
    ) {}

    /** @throws UpdateChatNotFoundException */
    public function createOrGetChat(
        int $sourceChatId,
        string $sourceServiceName
    ): ChocoChat {
        try {
            return $this->chocoRepository->findChatBySourceChatId(
                $sourceChatId,
                $sourceServiceName
            );
        } catch (ChocoChatNotFoundException) {
            $updateChat = $this->updateRepository->getChatBySourceDTO(
                new UpdateSourceDTO(
                    $sourceChatId,
                    $sourceServiceName
                )
            );
            $entity = new ChocoChat($updateChat);
            $this->chocoRepository->add($entity, true);

            return $entity;
        }
    }

    /** @throws UpdateUserNotFoundException */
    public function createOrGetUser(
        int $sourceUserId,
        string $sourceServiceName
    ): ChocoUser {
        try {
            return $this->chocoRepository->findUserBySourceUserId(
                $sourceUserId,
                $sourceServiceName
            );
        } catch (UserNotFoundException) {
            $updateUser = $this->updateRepository->getUserBySourceDTO(
                new UpdateSourceDTO(
                    $sourceUserId,
                    $sourceServiceName
                )
            );
            $entity = new ChocoUser($updateUser);
            $this->chocoRepository->add($entity, true);

            return $entity;
        }
    }
}
