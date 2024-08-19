<?php

declare(strict_types=1);

namespace App\Main\Domain\DTO;

final readonly class UpdateUserMutateDTO
{
    public function __construct(
        public bool $isBot,
        public string $firstName,
        public ?string $lastName,
        public ?string $username,
        public ?string $languageCode,
        public ?bool $canJoinGroups,
        public ?bool $canReadAllGroupMessages,
    ) {}
}
