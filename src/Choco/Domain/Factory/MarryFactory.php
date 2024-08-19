<?php

declare(strict_types=1);

namespace App\Choco\Domain\Factory;

use App\Choco\Domain\Entity\Choco\ChatMember;
use App\Choco\Domain\Entity\Choco\Marry;
use App\Choco\Domain\Exception\Marry\MarryException;
use App\Choco\Domain\Repository\MarryRepositoryInterface;

final readonly class MarryFactory
{
    public function __construct(
        private MarryRepositoryInterface $marryRepository
    ) {}

    /**
     * @param ChatMember[] $participants
     *
     * @throws MarryException
     */
    public function create(ChatMember $creator, array $participants): Marry
    {
        foreach ($participants as $participant) {
            if ($participant->isMarried()) {
                throw new MarryException(
                    sprintf(
                        'ChocoUser %s already married',
                        $participant->getUser()->getUpdateUser()->getFirstName()
                    )
                );
            }
        }

        $marry = new Marry();

        foreach ($participants as $participant) {
            $marry->addParticipant($participant->getUser());
        }

        $creator->acceptMarry();

        $marry->initiateMarryRequestEvent($creator);

        $this->marryRepository->add($marry);

        return $marry;
    }
}
