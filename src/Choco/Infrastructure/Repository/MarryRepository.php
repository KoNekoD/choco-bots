<?php

declare(strict_types=1);

namespace App\Choco\Infrastructure\Repository;

use App\Choco\Domain\Entity\Choco\Marry;
use App\Choco\Domain\Repository\MarryRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class MarryRepository
    extends ServiceEntityRepository
    implements MarryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Marry::class);
    }

    public function add(Marry $marry, bool $flush = false): void
    {
        $this->getEntityManager()->persist($marry);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Marry $marry, bool $flush = false): void
    {
        $this->getEntityManager()->remove($marry);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }
}
