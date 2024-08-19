<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_contact')]
class Contact
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', length: 26),
        ORM\GeneratedValue(strategy: 'NONE')
    ]
    private readonly string $id;

    public function __construct(
        #[ORM\Column(type: 'string')]
        private readonly string $phoneNumber,
        #[ORM\Column(type: 'string')]
        private readonly string $firstName,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $lastName,
        #[ORM\Column(type: 'integer', nullable: true)]
        private readonly ?int $userId,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $vcard,
    ) {
        $this->id = UlidService::generate();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getVcard(): ?string
    {
        return $this->vcard;
    }
}
