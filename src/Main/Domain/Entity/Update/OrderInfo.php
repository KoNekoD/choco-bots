<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_order_info')]
class OrderInfo
{
    #[ORM\Id, ORM\Column(type: 'string', length: 26), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    public function __construct(
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $name,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $phoneNumber,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $email,
        #[ORM\OneToOne(targetEntity: ShippingAddress::class)]
        #[ORM\JoinColumn(name: 'shipping_address_id', referencedColumnName: 'id')]
        private readonly ?ShippingAddress $shippingAddress,
    ) {
        $this->id = UlidService::generate();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getShippingAddress(): ?ShippingAddress
    {
        return $this->shippingAddress;
    }
}
