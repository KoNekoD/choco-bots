<?php

declare(strict_types=1);

namespace App\Main\Infrastructure\Repository;

use App\Main\Domain\DTO\UpdateSourceDTO;
use App\Main\Domain\Entity\Update\CallbackQuery;
use App\Main\Domain\Entity\Update\Update;
use App\Main\Domain\Entity\Update\UpdateChat;
use App\Main\Domain\Entity\Update\UpdateMessage;
use App\Main\Domain\Entity\Update\UpdateUser;
use App\Main\Domain\Enum\UpdateHandleStatusEnum;
use App\Main\Domain\Exception\UpdateEntities\UpdateChatNotFoundException;
use App\Main\Domain\Exception\UpdateEntities\UpdateMessageNotFoundException;
use App\Main\Domain\Exception\UpdateEntities\UpdateNotFoundException;
use App\Main\Domain\Exception\UpdateEntities\UpdateUserNotFoundException;
use App\Main\Domain\Repository\UpdateRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

final class UpdateRepository
    extends ServiceEntityRepository
    implements UpdateRepositoryInterface
{
    final public const GET_UPDATES_FOR_HANDLE_MAX_COUNT = 5000;

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

    public function freshFlush(): void
    {
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function isExistUpdateBySourceUpdateId(
        int $updateId,
        string $sourceServiceName,
        string $botId
    ): bool {
        $result = $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.sourceUpdateId = :sourceUpdateId')
            ->andWhere('u.sourceServiceName = :sourceServiceName')
            ->andWhere('u.botId = :botId')
            ->setParameter('sourceUpdateId', $updateId)
            ->setParameter('sourceServiceName', $sourceServiceName)
            ->setParameter('botId', $botId)
            ->getQuery()
            ->getSingleScalarResult();

        return $result > 0;
    }

    public function getLastUpdateId(string $sourceServiceName): int
    {
        return (int)$this->createQueryBuilder('u')
            ->select('u.sourceUpdateId')
            ->where('u.sourceServiceName = :sourceServiceName')
            ->orderBy('u.sourceUpdateId', 'DESC')
            ->setMaxResults(1)
            ->setParameter('sourceServiceName', $sourceServiceName)
            ->getQuery()
            ->getScalarResult();
    }

    /**
     * @inheritDoc
     * @throws NonUniqueResultException
     */
    public function findUpdateById(string $id): Update
    {
        try {
            /** @var Update $result */
            $result = $this->createQueryBuilder('u')
                ->where('u.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getSingleResult();

            return $result;
        } catch (NoResultException) {
            throw new UpdateNotFoundException();
        }
    }

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }

    public function getUpdatesForHandle(): array
    {
        /** @var Update[] $results */
        $results = $this->createQueryBuilder('u')
            ->where('u.handleStatus = :status')
            ->setParameter('status', UpdateHandleStatusEnum::PENDING)
            ->setMaxResults(self::GET_UPDATES_FOR_HANDLE_MAX_COUNT)
            ->getQuery()
            ->getResult();

        return $results;
    }

    /**
     * @inheritDoc
     * @throws NonUniqueResultException
     */
    public function getChatBySourceDTO(UpdateSourceDTO $DTO): UpdateChat
    {
        try {
            /** @var UpdateChat $result */
            $result = $this->getEntityManager()->createQueryBuilder()
                ->select('uc')
                ->from(UpdateChat::class, 'uc')
                ->where('uc.sourceChatId = :sourceChatId')
                ->andWhere('uc.sourceServiceName = :sourceServiceName')
                ->setParameter('sourceChatId', $DTO->sourceId)
                ->setParameter('sourceServiceName', $DTO->sourceServiceName)
                ->getQuery()
                ->getSingleResult();

            return $result;
        } catch (NoResultException) {
            throw new UpdateChatNotFoundException(
                sprintf(
                    'UpdateChat with sourceChatId %s and sourceServiceName %s not found',
                    $DTO->sourceId,
                    $DTO->sourceServiceName
                )
            );
        }
    }

    /**
     * @inheritDoc
     * @throws NonUniqueResultException
     */
    public function getMessageBySourceDTO(UpdateSourceDTO $DTO): UpdateMessage
    {
        try {
            /** @var UpdateMessage $result */
            $result = $this->getEntityManager()->createQueryBuilder()
                ->select('um')
                ->from(UpdateMessage::class, 'um')
                ->where('um.sourceMessageId = :sourceMessageId')
                ->andWhere('um.sourceServiceName = :sourceServiceName')
                ->setParameter('sourceMessageId', $DTO->sourceId)
                ->setParameter('sourceServiceName', $DTO->sourceServiceName)
                ->getQuery()
                ->getSingleResult();

            return $result;
        } catch (NoResultException) {
            throw new UpdateMessageNotFoundException(
                sprintf(
                    'UpdateMessage with sourceMessageId %s and sourceServiceName %s not found',
                    $DTO->sourceId,
                    $DTO->sourceServiceName
                )
            );
        }
    }

    /**
     * @inheritDoc
     * @throws NonUniqueResultException
     */
    public function getUserBySourceDTO(UpdateSourceDTO $DTO): UpdateUser
    {
        try {
            /** @var UpdateUser $result */
            $result = $this->getEntityManager()->createQueryBuilder()
                ->select('uu')
                ->from(UpdateUser::class, 'uu')
                ->where('uu.sourceUserId = :sourceUserId')
                ->andWhere('uu.sourceServiceName = :sourceServiceName')
                ->setParameter('sourceUserId', $DTO->sourceId)
                ->setParameter('sourceServiceName', $DTO->sourceServiceName)
                ->getQuery()
                ->getSingleResult();

            return $result;
        } catch (NoResultException) {
            throw new UpdateUserNotFoundException(
                sprintf(
                    'UpdateMessage with sourceMessageId %s and sourceServiceName %s not found',
                    $DTO->sourceId,
                    $DTO->sourceServiceName
                )
            );
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function isExistCallbackQueryByTelegramId(string $id): bool
    {
        $result = $this->getEntityManager()->createQueryBuilder()
            ->select('count(q.id)')
            ->from(CallbackQuery::class, 'q')
            ->where('q.telegramId = :telegram_id')
            ->setParameter('telegram_id', $id)
            ->getQuery()
            ->getSingleScalarResult();

        return $result > 0;
    }
}
