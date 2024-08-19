<?php

declare(strict_types=1);

namespace App\Choco\Infrastructure\Repository;

use App\Choco\Domain\Entity\Choco\ChatMember;
use App\Choco\Domain\Entity\Choco\ChatMemberRank;
use App\Choco\Domain\Entity\Choco\ChocoChat;
use App\Choco\Domain\Entity\Choco\ChocoUser;
use App\Choco\Domain\Enum\ChatMemberRankStatusEnum;
use App\Choco\Domain\Exception\ChocoEntity\ChatMemberException;
use App\Choco\Domain\Repository\ChatMemberRepositoryInterface;
use App\Choco\Domain\Repository\ChocoRepositoryInterface;
use App\Main\Domain\Entity\Update\Update;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ChatMemberRepository
    extends ServiceEntityRepository
    implements ChatMemberRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly TranslatorInterface $translator,
        private readonly ChocoRepositoryInterface $chocoRepository
    ) {
        parent::__construct($registry, ChatMember::class);
    }

    public function add(ChatMember $member, bool $flush = false): void
    {
        $this->getEntityManager()->persist($member);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function addRank(ChatMemberRank $rank, bool $flush = false): void
    {
        $this->getEntityManager()->persist($rank);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @inheritDoc
     * @throws NonUniqueResultException
     */
    public function findChatMemberById(string $id): ChatMember
    {
        try {
            /** @var ChatMember $member */
            $member = $this->getEntityManager()->createQueryBuilder()
                ->select('m')
                ->from(ChatMember::class, 'm')
                ->where('m.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getSingleResult();

            return $member;
        } catch (NoResultException) {
            throw new ChatMemberException(
                $this->translator->trans('MemberNotFound')
            );
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function findChatMemberByFirstMentionOrUsername(
        Update $update,
        string $username
    ): ChatMember {
        try {
            // @TODO Optimize this
            $ch = $update->getChat();

            if (null === $ch) {
                throw new Exception('Update chat is null');
            }

            $chocoChat = $this->getEntityManager()->createQueryBuilder()
                ->select('c')
                ->from(ChocoChat::class, 'c')
                ->innerJoin('c.updateChat', 'u')
                ->where('u.id = :update_chat_id')
                ->setParameter('update_chat_id', $ch->getId())
                ->getQuery()
                ->getSingleResult();

            if (
                null !== $update->getMessage()?->getEntities()
                && $update->getMessage()->getEntities() !== []
            ) {
                $user = null;
                foreach ($update->getMessage()->getEntities() as $entity) {
                    if ($entity->getUser() !== null) {
                        $user = $entity->getUser();
                        break;
                    }
                }

                if ($user !== null) {
                    // @TODO Optimize this
                    $chocoUser = $this->getEntityManager()->createQueryBuilder()
                        ->select('c')
                        ->from(ChocoUser::class, 'c')
                        ->innerJoin('c.updateUser', 'u')
                        ->where('u.id = :update_user_id')
                        ->setParameter(
                            'update_user_id',
                            $user->getId()
                        )
                        ->getQuery()
                        ->getSingleResult();

                    /** @var ChatMember $member */
                    $member = $this->getEntityManager()->createQueryBuilder()
                        ->select('m')
                        ->from(ChatMember::class, 'm')
                        ->where('m.user = :user')
                        ->andWhere('m.chat = :chat')
                        ->setParameter('chat', $chocoChat)
                        ->setParameter('user', $chocoUser)
                        ->getQuery()
                        ->getSingleResult();

                    return $member;
                }
            }

            /** @var ChatMember $member */
            $member = $this->getEntityManager()->createQueryBuilder()
                ->select('m')
                ->from(ChatMember::class, 'm')
                ->innerJoin('m.user', 'u')
                ->innerJoin('u.updateUser', 'uu')
                ->where('m.chat = :chat')
                ->andWhere('uu.username = :username')
                ->orderBy('uu.id', 'DESC')
                ->setMaxResults(1)
                ->setParameter('chat', $chocoChat)
                ->setParameter('username', $username)
                ->getQuery()
                ->getSingleResult();

            return $member;
        } catch (NoResultException) {
            throw new ChatMemberException(
                $this->translator->trans('MemberNotFound')
            );
        }
    }

    /**
     * @inheritDoc
     * @throws NonUniqueResultException
     */
    public function findChatMember(
        int $sourceChatId,
        int $sourceUserId,
        string $sourceServiceName
    ): ChatMember {
        try {
            /** @var ChatMember $member */
            $member = $this->getEntityManager()->createQueryBuilder()
                ->select('m')
                ->from(ChatMember::class, 'm')
                ->innerJoin('m.user', 'u')
                ->innerJoin('u.updateUser', 'uu')
                ->innerJoin('m.chat', 'c')
                ->innerJoin('c.updateChat', 'uc')
                ->where('uc.sourceChatId = :sourceChatId')
                ->andWhere('uu.sourceUserId = :sourceUserId')
                ->andWhere('uu.sourceServiceName = :sourceServiceName')
                ->setParameter('sourceChatId', $sourceChatId)
                ->setParameter('sourceUserId', $sourceUserId)
                ->setParameter('sourceServiceName', $sourceServiceName)
                ->getQuery()
                ->getSingleResult();

            return $member;
        } catch (NoResultException) {
            throw new ChatMemberException(
                sprintf(
                    'ChocoChat member not found with source chat id: %d, source user id: %d, source service name: %s',
                    $sourceChatId,
                    $sourceUserId,
                    $sourceServiceName
                )
            );
        }
    }

    public function getChatMembersWithPrivileges(
        int $sourceChatId,
        string $sourceServiceName
    ): array {
        /** @var ChatMember[] $members */
        $members = $this->getEntityManager()->createQueryBuilder()
            ->select('m')
            ->from(ChatMember::class, 'm')
            ->innerJoin('m.chat', 'c')
            ->innerJoin('m.rank', 'r')
            ->where('m.sourceServiceName = :sourceServiceName')
            ->andWhere('c.sourceChatId = :sourceChatId')
            ->andWhere('r.rank <> :memberRank')
            ->setParameter(
                'memberRank',
                ChatMemberRankStatusEnum::Member->value
            )
            ->setParameter('sourceChatId', $sourceChatId)
            ->setParameter('sourceServiceName', $sourceServiceName)
            ->getQuery()
            ->getResult();

        return $members;
    }

    /**
     * @throws Exception
     */
    public function findChatMembersByMentionOrUsername(
        Update $update,
        array $usernameList
    ): array {
        $usernamesForMethodTwoFetch = $usernameList;
        $chocoUsers = [];

        if (
            null !== $update->getMessage()?->getEntities()
            && $update->getMessage()->getEntities() !== []
        ) {
            foreach ($update->getMessage()->getEntities() as $entity) {
                if ($entity->getUser() !== null) {
                    $updateUser = $entity->getUser();

                    $chocoUser = $this->chocoRepository
                        ->findUserBySourceUserId(
                            $updateUser->getSourceUserId(),
                            $updateUser
                                ->getSourceServiceName()
                        );

                    $chocoUsers[] = $chocoUser;

                    foreach ($usernamesForMethodTwoFetch as $i => $item) {
                        if ($item === $entity->getUser()->getUsername()) {
                            unset($usernamesForMethodTwoFetch[$i]);
                        }
                    }
                }
            }
        }

        // @TODO refactor this
        $uc = $update->getChat();

        if (null === $uc) {
            throw new Exception('Update->chat cannot be null');
        }

        $chocoChat = $this->chocoRepository
            ->getChocoChatByUpdateChat($uc);

        // Method 1
        /** @var ChatMember[] $resultMethod1 */
        $resultMethod1 = $this->getEntityManager()->createQueryBuilder()
            ->select('m')
            ->from(ChatMember::class, 'm')
            ->where('m.user IN (:users)')
            ->andWhere('m.chat = :chat')
            ->setParameter('chat', $chocoChat)
            ->setParameter('users', $chocoUsers)
            ->getQuery()
            ->getResult();

        // Method 2
        /** @var ChatMember[] $resultMethod2 */
        $resultMethod2 = $this->getEntityManager()->createQueryBuilder()
            ->select('m')
            ->from(ChatMember::class, 'm')
            ->innerJoin('m.user', 'u')
            ->innerJoin('u.updateUser', 'uu')
            ->where('m.chat = :chat')
            ->andWhere('uu.username IN (:usernames)')
            ->orderBy('uu.id', 'DESC')
            ->setParameter('usernames', $usernamesForMethodTwoFetch)
            ->setParameter('chat', $chocoChat)
            ->getQuery()
            ->getResult();

        return [...$resultMethod1, ...$resultMethod2];
    }
}
