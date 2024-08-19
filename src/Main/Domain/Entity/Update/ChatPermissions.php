<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_chat_permissions')]
class ChatPermissions
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', length: 26),
        ORM\GeneratedValue(strategy: 'NONE')
    ]
    private readonly string $id;

    public function __construct(
        #[ORM\Column(type: 'boolean', nullable: true)]
        private readonly ?bool $canSendMessages,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private readonly ?bool $canSendMediaMessages,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private readonly ?bool $canSendPolls,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private readonly ?bool $canSendOtherMessages,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private readonly ?bool $canAddWebPagePreviews,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private readonly ?bool $canChangeInfo,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private readonly ?bool $canInviteUsers,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private readonly ?bool $canPinMessages,
    ) {
        $this->id = UlidService::generate();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCanSendMessages(): ?bool
    {
        return $this->canSendMessages;
    }

    public function getCanSendMediaMessages(): ?bool
    {
        return $this->canSendMediaMessages;
    }

    public function getCanSendPolls(): ?bool
    {
        return $this->canSendPolls;
    }

    public function getCanSendOtherMessages(): ?bool
    {
        return $this->canSendOtherMessages;
    }

    public function getCanAddWebPagePreviews(): ?bool
    {
        return $this->canAddWebPagePreviews;
    }

    public function getCanChangeInfo(): ?bool
    {
        return $this->canChangeInfo;
    }

    public function getCanInviteUsers(): ?bool
    {
        return $this->canInviteUsers;
    }

    public function getCanPinMessages(): ?bool
    {
        return $this->canPinMessages;
    }
}
