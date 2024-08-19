<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_mask_position')]
class MaskPosition
{
    #[ORM\Id, ORM\Column(type: 'string', length: 26), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    public function __construct(
        #[ORM\Column(type: 'string')]
        private readonly string $point,
        #[ORM\Column(type: 'float')]
        private readonly float $xShift,
        #[ORM\Column(type: 'float')]
        private readonly float $yShift,
        #[ORM\Column(type: 'float')]
        private readonly float $scale,
    ) {
        $this->id = UlidService::generate();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPoint(): string
    {
        return $this->point;
    }

    public function getXShift(): float
    {
        return $this->xShift;
    }

    public function getYShift(): float
    {
        return $this->yShift;
    }

    public function getScale(): float
    {
        return $this->scale;
    }
}
