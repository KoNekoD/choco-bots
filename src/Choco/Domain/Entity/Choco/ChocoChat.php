<?php

declare(strict_types=1);

namespace App\Choco\Domain\Entity\Choco;

use App\Main\Domain\Entity\Update\UpdateChat;
use App\Shared\Domain\Service\UlidService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity, ORM\Table(name: 'choco_chat')]
class ChocoChat
{
    #[ORM\Id, ORM\Column(type: 'string'), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    /**
     * @var Collection<int, ChatMember> $members
     */
    #[ORM\OneToMany(
        mappedBy: 'chat',
        targetEntity: ChatMember::class,
        cascade: ['persist', 'remove']
    )]
    private Collection $members;

    /** @var Collection<ChatMemberWarn> $warns */
    #[ORM\OneToMany(
        mappedBy: 'chat',
        targetEntity: ChatMemberWarn::class,
        cascade: ['persist', 'remove']
    )]
    private Collection $warns;

    #[ORM\OneToOne(
        inversedBy: 'chat',
        targetEntity: ChatConfiguration::class,
        cascade: ['persist', 'remove']
    )]
    private ChatConfiguration $configuration;

    public function __construct(
        #[ORM\OneToOne(targetEntity: UpdateChat::class)]
        #[ORM\JoinColumn(name: 'update_chat_id', referencedColumnName: 'id')]
        private readonly UpdateChat $updateChat
    ) {
        $this->id = UlidService::generate();
        $this->members = new ArrayCollection();
        $this->warns = new ArrayCollection();

        $this->configuration = new ChatConfiguration($this);
    }

    public function addMember(ChatMember $chatMember): void
    {
        $this->members->add($chatMember);
    }

    /** @return ChatMember[] */
    public function getMembers(): array
    {
        return $this->members->toArray();
    }

    public function getUpdateChat(): UpdateChat
    {
        return $this->updateChat;
    }

    /** @return ChatMemberWarn[] */
    public function getLastFiveWarns(): array
    {
        /** @var ChatMemberWarn[] $result */
        $result = array_slice(
            array_reverse(
                $this->warns->toArray()
            ),
            0,
            5
        );

        return $result;
    }

    /** @return ChatMemberWarn[] */
    public function getWarnsByWarnedMember(ChatMember $member): array
    {
        /** @var ChatMemberWarn[] $warns */
        $warns = $this->warns->toArray();

        return array_filter(
            $warns,
            static fn(
                ChatMemberWarn $warn
            ) => $warn->getWarned()->getId() === $member->getId()
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    /** @maybe @deprecated */
    public function getSourceChatId(): int
    {
        return $this->updateChat->getSourceChatId();
    }

    /** @maybe @deprecated */
    public function getSourceServiceName(): string
    {
        return $this->updateChat->getSourceServiceName();
    }

    public function getDefaultMuteTimeInSeconds(): int
    {
        return 60 * 60; // 1 hour @TODO Remove hardcode
    }

    public function getDefaultWarnCount(): int
    {
        return 3; // @TODO Remove hardcode
    }

    public function manageConfiguration(
        ?bool $notifyAboutNewChatMember = null,
        ?bool $muteEnabled = null,
    ): void {
        $this->getConfiguration()
            ->manage(
                notifyAboutNewChatMemberInSystem: $notifyAboutNewChatMember,
                muteEnabled: $muteEnabled
            );
    }

    public function getConfiguration(): ChatConfiguration
    {
        if (!isset($this->configuration)) {
            $this->configuration = new ChatConfiguration($this);
        }

        return $this->configuration;
    }
}
