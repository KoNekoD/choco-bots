<?php

declare(strict_types=1);

namespace App\Choco\Domain\Repository;

use App\Choco\Domain\Entity\Choco\Marry;

interface MarryRepositoryInterface
{
    public function add(Marry $marry, bool $flush = false): void;

    public function remove(Marry $marry, bool $flush = false): void;

    public function save(): void;
}
