<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_invoice')]
class Invoice
{
    #[ORM\Id, ORM\Column(type: 'string', length: 26), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    public function __construct(
        #[ORM\Column(type: 'string')]
        private readonly string $title,
        #[ORM\Column(type: 'string')]
        private readonly string $description,
        #[ORM\Column(type: 'string')]
        private readonly string $startParameter,
        #[ORM\Column(type: 'string')]
        private readonly string $currency,
        #[ORM\Column(type: 'integer')]
        private readonly int $totalAmount,
    ) {
        $this->id = UlidService::generate();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStartParameter(): string
    {
        return $this->startParameter;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getTotalAmount(): int
    {
        return $this->totalAmount;
    }
}
