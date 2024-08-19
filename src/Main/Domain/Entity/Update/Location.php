<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_location')]
class Location
{
    #[ORM\Id, ORM\Column(type: 'string', length: 26), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    public function __construct(
        #[ORM\Column(type: 'float')]
        private readonly float $longitude,
        #[ORM\Column(type: 'float')]
        private readonly float $latitude,
        #[ORM\Column(type: 'float', nullable: true)]
        private readonly ?float $horizontalAccuracy,
        #[ORM\Column(type: 'integer', nullable: true)]
        private readonly ?int $livePeriod,
        #[ORM\Column(type: 'integer', nullable: true)]
        private readonly ?int $heading,
        #[ORM\Column(type: 'integer', nullable: true)]
        private readonly ?int $proximityAlertRadius,
    ) {
        $this->id = UlidService::generate();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getHorizontalAccuracy(): ?float
    {
        return $this->horizontalAccuracy;
    }

    public function getLivePeriod(): ?int
    {
        return $this->livePeriod;
    }

    public function getHeading(): ?int
    {
        return $this->heading;
    }

    public function getProximityAlertRadius(): ?int
    {
        return $this->proximityAlertRadius;
    }
}
