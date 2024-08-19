<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Main\Domain\DTO\UpdateUserMutateDTO;
use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_update_user')]
class UpdateUser
{
    #[ORM\Id, ORM\Column(type: 'string', length: 26), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    public function __construct(
        #[ORM\Column(type: 'bigint')]
        private readonly int $sourceUserId,
        #[ORM\Column(type: 'string')]
        private readonly string $sourceServiceName,
        #[ORM\Column(type: 'boolean')]
        private bool $isBot,
        #[ORM\Column(type: 'string')]
        private string $firstName,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $lastName,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $username,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $languageCode,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $canJoinGroups,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $canReadAllGroupMessages,
    ) {
        $this->id = UlidService::generate();
    }

    public static function createFromMutateDTO(
        int $sourceUserId,
        string $sourceServiceName,
        UpdateUserMutateDTO $DTO
    ): self {
        return new self(
            sourceUserId: $sourceUserId,
            sourceServiceName: $sourceServiceName,
            isBot: $DTO->isBot,
            firstName: $DTO->firstName,
            lastName: $DTO->lastName,
            username: $DTO->username,
            languageCode: $DTO->languageCode,
            canJoinGroups: $DTO->canJoinGroups,
            canReadAllGroupMessages: $DTO->canReadAllGroupMessages,
        );
    }

    public function mutate(UpdateUserMutateDTO $DTO): void
    {
        $this->isBot = $DTO->isBot;
        $this->firstName = $DTO->firstName;
        $this->lastName = $DTO->lastName;
        $this->username = $DTO->username;
        $this->languageCode = $DTO->languageCode;
        $this->canJoinGroups = $DTO->canJoinGroups;
        $this->canReadAllGroupMessages = $DTO->canReadAllGroupMessages;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSourceUserId(): int
    {
        return $this->sourceUserId;
    }

    public function getSourceServiceName(): string
    {
        return $this->sourceServiceName;
    }

    public function isBot(): bool
    {
        return $this->isBot;
    }

    public function getFirstName(): string
    {
        return htmlentities($this->firstName);
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }

    public function getCanJoinGroups(): ?bool
    {
        return $this->canJoinGroups;
    }

    public function getCanReadAllGroupMessages(): ?bool
    {
        return $this->canReadAllGroupMessages;
    }
}
