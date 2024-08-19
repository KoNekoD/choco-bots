<?php

declare(strict_types=1);

namespace App\Choco\Domain\DTO;

final readonly class RequiredChatConfigurationDTO
{
    public function __construct(public bool $muteEnabled = false) {}

    public static function muteRequired(): self
    {
        return new self(muteEnabled: true);
    }
}
