<?php

declare(strict_types=1);

namespace App\Choco\Infrastructure\Repository;

use App\Choco\Domain\DTO\ChatMemberMessagesStatDTO;
use App\Choco\Domain\Entity\Choco\ChatMember;
use App\Choco\Domain\Entity\Choco\ChocoChat;
use App\Choco\Domain\Entity\Choco\ChocoUser;
use App\Choco\Domain\Entity\Choco\Marry;
use App\Choco\Domain\Exception\ChocoBaseException;
use App\Choco\Domain\Exception\ChocoEntity\ChocoChatNotFoundException;
use App\Choco\Domain\Exception\ChocoEntity\UserNotFoundException;
use App\Choco\Domain\Repository\ChocoRepositoryInterface;
use App\Main\Domain\Entity\Update\Update;
use App\Main\Domain\Entity\Update\UpdateChat;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

final class ChocoRepository
    extends ServiceEntityRepository
    implements ChocoRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Update::class);
    }

    public function add(object $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(object $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @inheritDoc
     * @throws NonUniqueResultException
     */
    public function findChatBySourceChatId(
        int $sourceChatId,
        string $sourceServiceName
    ): ChocoChat {
        try {
            /** @var ChocoChat $result */
            $result = $this->getEntityManager()->createQueryBuilder()
                ->select('c')
                ->from(ChocoChat::class, 'c')
                ->innerJoin('c.updateChat', 'uc')
                ->where('uc.sourceChatId = :sourceChatId')
                ->andWhere('uc.sourceServiceName = :sourceServiceName')
                ->setParameter('sourceChatId', $sourceChatId)
                ->setParameter('sourceServiceName', $sourceServiceName)
                ->getQuery()
                ->getSingleResult();

            return $result;
        } catch (NoResultException) {
            throw new ChocoChatNotFoundException(
                sprintf(
                    'Choco/ChocoChat not found with sourceChatId %s',
                    $sourceChatId
                )
            );
        }
    }

    /**
     * @inheritDoc
     * @throws NonUniqueResultException
     */
    public function findUserBySourceUserId(
        int $sourceUserId,
        string $sourceServiceName
    ): ChocoUser {
        try {
            /** @var ChocoUser $result */
            $result = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from(ChocoUser::class, 'u')
                ->innerJoin('u.updateUser', 'uu')
                ->where('uu.sourceUserId = :sourceUserId')
                ->andWhere('uu.sourceServiceName = :sourceServiceName')
                ->setParameter('sourceUserId', $sourceUserId)
                ->setParameter('sourceServiceName', $sourceServiceName)
                ->getQuery()
                ->getSingleResult();

            return $result;
        } catch (NoResultException) {
            throw new UserNotFoundException(
                sprintf(
                    'ChocoUser with source user id: %s not found',
                    $sourceUserId
                )
            );
        }
    }

    /**
     * @inheritDoc
     * @throws NonUniqueResultException
     */
    public function findUserById(string $id): ChocoUser
    {
        try {
            /** @var ChocoUser $result */
            $result = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from(ChocoUser::class, 'u')
                ->where('u.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getSingleResult();

            return $result;
        } catch (NoResultException) {
            throw new UserNotFoundException(
                sprintf('ChocoUser with id: %s not found', $id)
            );
        }
    }

    /**
     * @throws Exception
     */
    public function getMessagesCountAggregatedByChatMemberAndTimeRange(
        DateTimeImmutable $fromDate,
        DateTimeImmutable $toDate,
        ChatMember $who
    ): int {
        $sql = <<<SQL
SELECT count(um.id)
FROM updates_update_message um
LEFT JOIN updates_update_user uu ON um.from_id = uu.id
LEFT JOIN updates_update_chat uc ON um.chat_id = uc.id
WHERE (um.created_at BETWEEN :from_date AND :to_date)
  AND uu.source_user_id = :source_user_id
  AND uc.source_chat_id = :source_chat_id
SQL;

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue(
            'from_date',
            $fromDate->format('Y-m-d H:i:s')
        );
        $stmt->bindValue(
            'to_date',
            $toDate->format('Y-m-d H:i:s')
        );
        $stmt->bindValue(
            'source_user_id',
            $who->getUser()->getUpdateUser()->getSourceUserId()
        );
        $stmt->bindValue(
            'source_chat_id',
            $who->getChat()->getUpdateChat()->getSourceChatId()
        );
        $result = $stmt->executeQuery();

        /** @var int $fetchedResult */
        $fetchedResult = $result->fetchOne();

        return $fetchedResult;
    }

    /**
     * @throws Exception
     * @throws ChocoBaseException
     */
    public function getMessagesStatsAggregatedByChatAndTimeRange(
        DateTimeImmutable $fromDate,
        DateTimeImmutable $toDate,
        ChocoChat $chat
    ): array {
        $sql = <<<SQL
SELECT cu.id as choco_user_id, count(um.id) as quantity FROM updates_update_message um
    LEFT JOIN updates_update_user uu ON um.from_id = uu.id
    LEFT JOIN updates_update_chat uc ON um.chat_id = uc.id
    LEFT JOIN choco_user cu on uu.id = cu.update_user_id
WHERE
    (um.created_at BETWEEN :from_date AND :to_date) AND
        uc.source_chat_id = :source_chat_id
GROUP BY cu.id
ORDER BY count(um.id) DESC
LIMIT 10
SQL;

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);

        $stmt->bindValue(
            'from_date',
            $fromDate->format('Y-m-d H:i:s')
        );
        $stmt->bindValue(
            'to_date',
            $toDate->format('Y-m-d H:i:s')
        );
        $stmt->bindValue(
            'source_chat_id',
            $chat->getSourceChatId()
        );

        $result = $stmt->executeQuery();

        /** @var array<int, array{
         *     choco_user_id: string,
         *     quantity: int
         * }> $rawStats
         */
        $rawStats = $result->fetchAllAssociative();
        //        $rawStats = $this->getEntityManager()->createQueryBuilder()
        //            ->select("uuu.id as chocoUserId, count(m.id) as quantity")
        //            ->from("Choco:Update\UpdateMessage", 'u')
        //            ->leftJoin("u.message", 'm') // Choco/Message
        //            ->leftJoin('u.chat', 'uc') // Update/ChocoChat
        //            ->leftJoin('u.from', 'uu')
        //            ->leftJoin('uu.user', 'uuu')
        //            ->groupBy("uuu.id")
        //            ->orderBy('count(m.id)', 'DESC')
        //            ->where('m.createdAt BETWEEN :from AND :to')
        //            ->andWhere('uc.chat = :chat')
        //            ->setParameters([
        //                'from' => $fromDate,
        //                'to' => $toDate,
        //                'chat' => $chat
        //            ])
        //            ->setMaxResults(10)
        //            ->getQuery()
        //            ->getResult();

        $chocoUsersIds = [];
        foreach ($rawStats as $rawStat) {
            $chocoUsersIds[] = $rawStat['choco_user_id'];
        }

        /** @var ChocoUser[] $chocoUsers */
        $chocoUsers = $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from(ChocoUser::class, 'u')
            ->where('u.id IN (:ids)')
            ->setParameter('ids', $chocoUsersIds)
            ->getQuery()
            ->getResult();

        if (count($chocoUsers) !== count($rawStats)) {
            throw new ChocoBaseException(
                'INCORRECT QUERY. $chocoUsers and $rawStats count MUST be equal'
            );
        }

        $stats = [];

        foreach ($rawStats as $rawStat) {
            foreach ($chocoUsers as $chocoUser) {
                if ($rawStat['choco_user_id'] === $chocoUser->getId()) {
                    $stats[] = new ChatMemberMessagesStatDTO(
                        $chocoUser,
                        $rawStat['quantity']
                    );
                }
            }
        }

        return $stats;
    }

    public function findById(string $id): ?Update
    {
        /** @var ?Update $result */
        $result = $this->find($id);

        return $result;
    }

    public function getMarriesByChat(ChocoChat $chat): array
    {
        /** @var Marry[] $result */
        $result = $this->getEntityManager()->createQueryBuilder()
            ->select('m')
            ->from(Marry::class, 'm')
            ->where('c.chat = :chat')
            ->setParameter('chat', $chat)
            ->innerJoin('m.participants', 'p')
            ->innerJoin('p.chats', 'c')
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * @inheritDoc
     * @throws NonUniqueResultException
     */
    public function getChocoChatByUpdateChat(UpdateChat $updateChat): ChocoChat
    {
        /** @var ?ChocoChat $chat */
        $chat = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from(ChocoChat::class, 'c')
            ->where('c.updateChat = :chat')
            ->setParameter('chat', $updateChat)
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $chat) {
            throw new ChocoChatNotFoundException(
                sprintf(
                    'UpdateChat %s does not have ChocoChat',
                    $updateChat->getId()
                )
            );
        }

        return $chat;
    }
}
