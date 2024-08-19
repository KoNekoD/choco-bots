<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_chat_photo')]
class ChatPhoto
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $fileId;

    public function __construct(string $smallFileId, string $bigFileId)
    {
        $this->fileId = $smallFileId.'___'.$bigFileId;
    }

    public function getCompositeFileId(): string
    {
        return $this->fileId;
    }

    public function getSmallFileId(): string
    {
        return explode('___', $this->fileId)[0];
    }

    public function getBigFileId(): string
    {
        return explode('___', $this->fileId)[1];
    }
}
