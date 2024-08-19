<?php

declare(strict_types=1);

namespace App\Choco\Domain\Entity\Choco;

use App\Choco\Domain\Enum\MarryStatusEnum;
use App\Choco\Domain\Enum\UserMarryStatusEnum;
use App\Choco\Domain\Exception\Marry\MarryException;
use App\Main\Domain\Entity\Update\UpdateUser;
use App\Shared\Domain\Service\UlidService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity, ORM\Table(name: 'choco_user')]
class ChocoUser
{
    #[ORM\Id, ORM\Column(type: 'string'), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    /**
     * @var Collection<int, ChatMember> $chats
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ChatMember::class)]
    private Collection $chats;

    #[ORM\ManyToOne(targetEntity: Marry::class, inversedBy: 'participants')]
    #[ORM\JoinColumn(name: 'marry_id', referencedColumnName: 'id')]
    private ?Marry $marry = null;

    #[ORM\Column(
        type: 'string',
        nullable: true,
        enumType: UserMarryStatusEnum::class
    )]
    private ?UserMarryStatusEnum $marryParticipantStatus = null;

    public function __construct(
        #[ORM\OneToOne(targetEntity: UpdateUser::class)]
        #[ORM\JoinColumn(name: 'update_user_id', referencedColumnName: 'id')]
        private readonly UpdateUser $updateUser
    ) {
        $this->id = UlidService::generate();
        $this->chats = new ArrayCollection();
    }

    public function getSourceUserId(): int
    {
        return $this->updateUser->getSourceUserId();
    }

    public function getSourceServiceName(): string
    {
        return $this->updateUser->getSourceServiceName();
    }

    /** @return ChocoChat[] */
    public function getChats(): array
    {
        $chats = [];

        foreach ($this->chats as $chatMember) {
            $chats[] = $chatMember->getChat();
        }

        return $chats;
    }

    public function getRankByChat(ChocoChat $chat): ChatMemberRank
    {
        /** @var ChatMember $chatMember */
        $chatMember = $this->chats->filter(
            static function (ChatMember $chatMember) use ($chat): bool {
                return $chatMember->getChat()->getId() === $chat->getId();
            }
        )->first();

        return $chatMember->getRank();
    }

    public function getId(): string
    {
        return $this->id;
    }

    /** @throws MarryException */
    public function trySendMarryRequest(Marry $marry): void
    {
        if ($this->isMarried()) {
            throw new MarryException(
                sprintf(
                    'ChocoUser %s already married',
                    $this->getUpdateUser()->getFirstName()
                )
            );
        }
        $this->marry = $marry;
        $this->marryParticipantStatus = UserMarryStatusEnum::NOT_ACCEPTED;
    }

    public function isMarried(): bool
    {
        return
            null !== $this->marry
            && UserMarryStatusEnum::ACCEPTED === $this->marryParticipantStatus
            && MarryStatusEnum::MARRIED === $this->marry
                ->getMarryGeneralStatus();
    }

    public function getUpdateUser(): UpdateUser
    {
        return $this->updateUser;
    }

    /** @throws MarryException */
    public function acceptMarry(int|string $sourceChatIdForNotify): void
    {
        if (null === $this->marry) {
            throw new MarryException(
                sprintf(
                    'ChocoUser %s does not have marry requests',
                    $this->getUpdateUser()->getFirstName()
                )
            );
        }
        $this->marryParticipantStatus = UserMarryStatusEnum::ACCEPTED;
        try {
            $this->marry->tryAcceptGeneralMarry(
                $sourceChatIdForNotify
            );
        } catch (MarryException) {
            return;
        }
    }

    public function divorceMarry(): void
    {
        $this->marry = null;
        $this->marryParticipantStatus = null;
    }

    public function getMarry(): ?Marry
    {
        return $this->marry;
    }

    public function getMarryParticipantStatus(): ?UserMarryStatusEnum
    {
        return $this->marryParticipantStatus;
    }

    public function isMarryParticipantStatusAccepted(): bool
    {
        return UserMarryStatusEnum::ACCEPTED === $this->marryParticipantStatus;
    }

    public function getUpdateUsername(): ?string
    {
        return $this->updateUser->getUsername();
    }
}
