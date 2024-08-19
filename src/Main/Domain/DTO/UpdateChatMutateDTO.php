<?php

declare(strict_types=1);

namespace App\Main\Domain\DTO;

use App\Main\Domain\Entity\Update\ChatLocation;
use App\Main\Domain\Entity\Update\ChatPermissions;
use App\Main\Domain\Entity\Update\ChatPhoto;
use App\Main\Domain\Entity\Update\UpdateMessage;
use App\Main\Domain\Enum\UpdateChatTypeEnum;

final readonly class UpdateChatMutateDTO
{
    public function __construct(
        public UpdateChatTypeEnum $type,
        public ?string $title,
        public ?string $username,
        public ?string $firstName,
        public ?string $lastName,
        public ?ChatPhoto $photo,
        public ?string $bio,
        public ?bool $hasPrivateForwards,
        public ?string $description,
        public ?string $inviteLink,
        public ?UpdateMessage $pinnedMessage,
        public ?ChatPermissions $permissions,
        public ?int $slowModeDelay,
        public ?bool $hasProtectedContent,
        public ?string $stickerSetName,
        public ?bool $canSetStickerSet,
        public ?int $linkedChatId,
        public ?ChatLocation $location,
    ) {}
}
