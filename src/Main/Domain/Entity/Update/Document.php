<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_document')]
class Document
{
    #[ORM\Id, ORM\Column(type: 'string', length: 26), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    public function __construct(
        #[ORM\Column(type: 'string')]
        private readonly string $fileId,
        #[ORM\Column(type: 'string')]
        private readonly string $fileUniqueId,
        #[ORM\OneToOne(targetEntity: PhotoSize::class)]
        #[ORM\JoinColumn(name: 'thumb_id', referencedColumnName: 'id')]
        private readonly ?PhotoSize $thumb,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $fileName,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $mimeType,
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

    public function getThumb(): ?PhotoSize
    {
        return $this->thumb;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }
}
