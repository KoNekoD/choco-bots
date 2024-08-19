<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_shipping_address')]
class ShippingAddress
{
    #[ORM\Id, ORM\Column(type: 'string', length: 26), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    public function __construct(
        #[ORM\Column(type: 'string')]
        private readonly string $countryCode,
        #[ORM\Column(type: 'string')]
        private readonly string $state,
        #[ORM\Column(type: 'string')]
        private readonly string $city,
        #[ORM\Column(type: 'string')]
        private readonly string $streetLine1,
        #[ORM\Column(type: 'string')]
        private readonly string $streetLine2,
        #[ORM\Column(type: 'string')]
        private readonly string $postCode,
    ) {
        $this->id = UlidService::generate();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getStreetLine1(): string
    {
        return $this->streetLine1;
    }

    public function getStreetLine2(): string
    {
        return $this->streetLine2;
    }

    public function getPostCode(): string
    {
        return $this->postCode;
    }
}
