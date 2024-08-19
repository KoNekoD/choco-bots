<?php

declare(strict_types=1);

namespace App\Choco\Domain\Entity\Choco;

use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity, ORM\Table(name: 'choco_chat_configuration')]
class ChatConfiguration
{
    #[ORM\Id, ORM\Column(type: 'string'), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    #[ORM\Column(type: 'boolean')]
    private bool $notifyAboutNewChatMemberInSystem = true;

    #[ORM\Column(type: 'boolean')]
    private bool $muteEnabled = false;

    public function __construct(
        #[ORM\OneToOne(
            mappedBy: 'configuration',
            targetEntity: ChocoChat::class
        )]
        private readonly ChocoChat $chat
    ) {
        $this->id = UlidService::generate();
    }

    public function manage(
        ?bool $notifyAboutNewChatMemberInSystem,
        ?bool $muteEnabled,
    ): void {
        if (null !== $notifyAboutNewChatMemberInSystem) {
            $this->notifyAboutNewChatMemberInSystem =
                $notifyAboutNewChatMemberInSystem;
        }

        if (null !== $muteEnabled) {
            $this->muteEnabled = $muteEnabled;
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getChat(): ChocoChat
    {
        return $this->chat;
    }

    public function isNotifyAboutNewChatMemberInSystem(): bool
    {
        return $this->notifyAboutNewChatMemberInSystem;
    }

    public function isMuteEnabled(): bool
    {
        return $this->muteEnabled;
    }
}
