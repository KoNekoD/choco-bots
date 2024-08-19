<?php

declare(strict_types=1);

namespace App\Choco\Domain\Entity\Choco;

use App\Choco\Domain\Enum\MarryStatusEnum;
use App\Choco\Domain\Enum\UserMarryStatusEnum;
use App\Choco\Domain\Event\Marry\MarryRequestEvent;
use App\Choco\Domain\Event\Marry\SuccessfullyMarriedEvent;
use App\Choco\Domain\Exception\Marry\MarryException;
use App\Shared\Domain\Entity\Aggregate;
use App\Shared\Domain\Service\UlidService;
use Carbon\Carbon;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity, ORM\Table(name: 'choco_marry')]
class Marry
    extends Aggregate
{
    #[ORM\Id, ORM\Column(type: 'string'), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'string', enumType: MarryStatusEnum::class)]
    private MarryStatusEnum $marryGeneralStatus;

    /** @var Collection<int, ChocoUser> */
    #[ORM\OneToMany(mappedBy: 'marry', targetEntity: ChocoUser::class)]
    private Collection $participants;

    public function __construct()
    {
        $this->id = UlidService::generate();
        $this->createdAt = Carbon::now()->toDateTimeImmutable();
        $this->marryGeneralStatus = MarryStatusEnum::MARRY_REQUEST;
        $this->participants = new ArrayCollection();
    }

    public function initiateMarryRequestEvent(ChatMember $creator): void
    {
        $ids = [];
        foreach ($this->participants as $participant) {
            $ids[] = $participant->getId();
        }

        $this->raise(
            new MarryRequestEvent(
                $ids,
                $creator->getId()
            )
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /** @throws MarryException */
    public function tryAcceptGeneralMarry(
        int|string $sourceChatIdForNotify
    ): void {
        foreach ($this->getParticipants() as $participant) {
            if (UserMarryStatusEnum::ACCEPTED !== $participant->getMarryParticipantStatus(
                )) {
                throw new MarryException(
                    sprintf(
                        'Participant %s is not accepted marry request',
                        $participant->getUpdateUser()->getFirstName()
                    )
                );
            }
        }
        $this->marryGeneralStatus = MarryStatusEnum::MARRIED;
        $ids = [];
        foreach ($this->getParticipants() as $participant) {
            $ids[] = $participant->getId();
        }
        $this->raise(
            new SuccessfullyMarriedEvent($ids, $sourceChatIdForNotify)
        );
    }

    /** @return ChocoUser[] */
    public function getParticipants(): array
    {
        return $this->participants->toArray();
    }

    public function getMarryGeneralStatus(): MarryStatusEnum
    {
        return $this->marryGeneralStatus;
    }

    public function isMarryGeneralStatusMarried(): bool
    {
        return MarryStatusEnum::MARRIED === $this->marryGeneralStatus;
    }

    /** @throws MarryException */
    public function addParticipant(ChocoUser $participant): void
    {
        $this->participants[] = $participant;
        $participant->trySendMarryRequest($this);
    }

    public function divorce(): void
    {
        foreach ($this->participants as $participant) {
            $participant->divorceMarry();
            $this->removeParticipant($participant);
        }

        $this->marryGeneralStatus = MarryStatusEnum::DIVORCE;
    }

    public function removeParticipant(ChocoUser $participant): void
    {
        foreach ($this->participants as $marriedParticipant) {
            if ($marriedParticipant->getId() === $participant->getId()) {
                $this->participants->removeElement($marriedParticipant);
            }
        }
    }

    public function getFullParticipantsFirstNamesString(
        string $separator = ' + '
    ): string {
        $participantFirstNames = [];
        foreach ($this->participants as $participant) {
            $participantFirstNames[] = $participant->getUpdateUser(
            )->getFirstName();
        }

        return implode($separator, $participantFirstNames);
    }
}
