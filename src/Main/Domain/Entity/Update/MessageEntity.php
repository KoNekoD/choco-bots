<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_message_entity')]
class MessageEntity
{
    #[ORM\Id, ORM\Column(type: 'string', length: 26), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    public function __construct(
        #[ORM\Column(type: 'string')]
        private readonly string $type,
        #[ORM\Column(name: 'message_offset', type: 'integer')]
        private readonly int $offset,
        #[ORM\Column(type: 'integer')]
        private readonly int $length,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $url,
        #[ORM\ManyToOne(targetEntity: UpdateUser::class)]
        #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
        private readonly ?UpdateUser $user,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $language,
    ) {
        $this->id = UlidService::generate();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getUser(): ?UpdateUser
    {
        return $this->user;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }
}
