<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Shared\Domain\Service\UlidService;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_poll')]
class Poll
{
    #[ORM\Id, ORM\Column(type: 'string', length: 26), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    /**
     * @param string $sourcePollId
     * @param string $question
     * @param Collection<int, PollOption> $options
     * @param int $totalVoterCount
     * @param bool $isClosed
     * @param bool $isAnonymous
     * @param string $type
     * @param bool $allowMultipleAnswers
     * @param int|null $correctOptionId
     */
    public function __construct(
        #[ORM\Column(type: 'string')]
        private readonly string $sourcePollId,
        #[ORM\Column(type: 'string')]
        private readonly string $question,
        #[ORM\JoinTable(name: 'updates_poll_polls_poll_options')]
        #[ORM\JoinColumn(name: 'poll_id', referencedColumnName: 'id')]
        #[ORM\InverseJoinColumn(name: 'option_id', referencedColumnName: 'id', unique: true)]
        #[ORM\ManyToMany(targetEntity: PollOption::class)]
        private Collection $options, // Collection cannot be readonly
        #[ORM\Column(type: 'integer')]
        private readonly int $totalVoterCount,
        #[ORM\Column(type: 'boolean')]
        private readonly bool $isClosed,
        #[ORM\Column(type: 'boolean')]
        private readonly bool $isAnonymous,
        #[ORM\Column(type: 'string')]
        private readonly string $type,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private readonly ?bool $allowMultipleAnswers,
        #[ORM\Column(type: 'integer', nullable: true)]
        private readonly ?int $correctOptionId,
    ) {
        $this->id = UlidService::generate();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSourcePollId(): string
    {
        return $this->sourcePollId;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    /** @return PollOption[] */
    public function getOptions(): array
    {
        return $this->options->toArray();
    }

    public function getTotalVoterCount(): int
    {
        return $this->totalVoterCount;
    }

    public function isClosed(): bool
    {
        return $this->isClosed;
    }

    public function isAnonymous(): bool
    {
        return $this->isAnonymous;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isAllowMultipleAnswers(): bool
    {
        return (bool)$this->allowMultipleAnswers;
    }

    public function getCorrectOptionId(): ?int
    {
        return $this->correctOptionId;
    }
}
