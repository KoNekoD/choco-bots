<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_chat_location')]
class ChatLocation
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', length: 26),
        ORM\GeneratedValue(strategy: 'NONE')
    ]
    private readonly string $id;

    public function __construct(
        #[ORM\OneToOne(targetEntity: Location::class)]
        #[ORM\JoinColumn(name: 'location_id', referencedColumnName: 'id')]
        private readonly Location $location,
        #[ORM\Column(type: 'string')]
        private readonly string $address,
    ) {
        $this->id = UlidService::generate();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getAddress(): string
    {
        return $this->address;
    }
}
