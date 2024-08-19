<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_photo_size')]
class PhotoSize
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

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }
}
