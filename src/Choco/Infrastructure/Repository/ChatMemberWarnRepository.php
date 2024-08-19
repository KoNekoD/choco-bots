<?php

declare(strict_types=1);

namespace App\Choco\Infrastructure\Repository;

use App\Choco\Domain\Entity\Choco\ChatMemberWarn;
use App\Choco\Domain\Repository\ChatMemberWarnRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ChatMemberWarnRepository
    extends ServiceEntityRepository
    implements ChatMemberWarnRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, ChatMemberWarn::class);
    }

    public function add(ChatMemberWarn $warn, bool $flush = false): void
    {
        $this->getEntityManager()->persist($warn);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
