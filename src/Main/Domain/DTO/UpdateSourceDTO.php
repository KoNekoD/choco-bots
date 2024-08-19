<?php

declare(strict_types=1);

namespace App\Main\Domain\DTO;

final readonly class UpdateSourceDTO
{
    public function __construct(
        public int $sourceId,
        public string $sourceServiceName
    ) {}
}
