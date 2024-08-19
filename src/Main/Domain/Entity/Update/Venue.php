<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_venue')]
class Venue
{
    #[ORM\Id, ORM\Column(type: 'string', length: 26), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    public function __construct(
        #[ORM\OneToOne(targetEntity: Location::class)]
        #[ORM\JoinColumn(name: 'location_id', referencedColumnName: 'id')]
        private readonly Location $location,
        #[ORM\Column(type: 'string')]
        private readonly string $title,
        #[ORM\Column(type: 'string')]
        private readonly string $address,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $foursquareId,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $foursquareType,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $googlePlaceId,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $googlePlaceType,
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getFoursquareId(): ?string
    {
        return $this->foursquareId;
    }

    public function getFoursquareType(): ?string
    {
        return $this->foursquareType;
    }

    public function getGooglePlaceId(): ?string
    {
        return $this->googlePlaceId;
    }

    public function getGooglePlaceType(): ?string
    {
        return $this->googlePlaceType;
    }
}
