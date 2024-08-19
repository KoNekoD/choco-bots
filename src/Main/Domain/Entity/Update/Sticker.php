<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_sticker')]
class Sticker
{
    #[ORM\Id, ORM\Column(type: 'string', length: 26), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    public function __construct(
        #[ORM\Column(type: 'string')]
        private readonly string $fileId,
        #[ORM\Column(type: 'string')]
        private readonly string $fileUniqueId,
        #[ORM\Column(type: 'integer')]
        private readonly int $width,
        #[ORM\Column(type: 'integer')]
        private readonly int $height,
        #[ORM\Column(type: 'boolean')]
        private readonly bool $isAnimated,
        #[ORM\OneToOne(targetEntity: PhotoSize::class)]
        #[ORM\JoinColumn(name: 'thumb_id', referencedColumnName: 'id')]
        private readonly ?PhotoSize $thumb,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $emoji,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $setName,
        #[ORM\OneToOne(targetEntity: MaskPosition::class)]
        #[ORM\JoinColumn(name: 'mask_position_id', referencedColumnName: 'id')]
        private readonly ?MaskPosition $maskPosition,
        #[ORM\Column(type: 'integer', nullable: true)]
        private readonly ?int $fileSize,
    ) {
        $this->id = UlidService::generate();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFileId(): string
    {
        return $this->fileId;
    }

    public function getFileUniqueId(): string
    {
        return $this->fileUniqueId;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function isAnimated(): bool
    {
        return $this->isAnimated;
    }

    public function getThumb(): ?PhotoSize
    {
        return $this->thumb;
    }

    public function getEmoji(): ?string
    {
        return $this->emoji;
    }

    public function getSetName(): ?string
    {
        return $this->setName;
    }

    public function getMaskPosition(): ?MaskPosition
    {
        return $this->maskPosition;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }
}
