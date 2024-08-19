<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Main\Domain\DTO\UpdateChatMutateDTO;
use App\Main\Domain\Enum\UpdateChatTypeEnum;
use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_update_chat')]
class UpdateChat
{
    #[ORM\Id, ORM\Column(type: 'string', length: 26), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    public function __construct(
        #[ORM\Column(type: 'bigint', unique: true)]
        private readonly int $sourceChatId,
        #[ORM\Column(type: 'string')]
        private readonly string $sourceServiceName,
        #[ORM\Column(type: 'string', enumType: UpdateChatTypeEnum::class)]
        private UpdateChatTypeEnum $type,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $title,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $username,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $firstName,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $lastName,
        #[ORM\OneToOne(targetEntity: ChatPhoto::class)]
        #[ORM\JoinColumn(name: 'photo_id', referencedColumnName: 'file_id')]
        private ?ChatPhoto $photo,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $bio,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $hasPrivateForwards,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $description,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $inviteLink,
        #[ORM\OneToOne(targetEntity: UpdateMessage::class)]
        #[ORM\JoinColumn(name: 'pinned_message_id', referencedColumnName: 'id')]
        private ?UpdateMessage $pinnedMessage,
        #[ORM\OneToOne(targetEntity: ChatPermissions::class)]
        #[ORM\JoinColumn(name: 'permissions_id', referencedColumnName: 'id')]
        private ?ChatPermissions $permissions,
        #[ORM\Column(type: 'integer', nullable: true)]
        private ?int $slowModeDelay,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $hasProtectedContent,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $stickerSetName,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $canSetStickerSet,
        #[ORM\Column(type: 'integer', nullable: true)]
        private ?int $linkedChatId,
        #[ORM\OneToOne(targetEntity: ChatLocation::class)]
        #[ORM\JoinColumn(name: 'location_id', referencedColumnName: 'id')]
        private ?ChatLocation $location,
    ) {
        $this->id = UlidService::generate();
    }

    public static function createFromMutateDTO(
        int $sourceChatId,
        string $sourceServiceName,
        UpdateChatMutateDTO $DTO
    ): self {
        return new self(
            sourceChatId: $sourceChatId,
            sourceServiceName: $sourceServiceName,
            type: $DTO->type,
            title: $DTO->title,
            username: $DTO->username,
            firstName: $DTO->firstName,
            lastName: $DTO->lastName,
            photo: $DTO->photo,
            bio: $DTO->bio,
            hasPrivateForwards: $DTO->hasPrivateForwards,
            description: $DTO->description,
            inviteLink: $DTO->inviteLink,
            pinnedMessage: $DTO->pinnedMessage,
            permissions: $DTO->permissions,
            slowModeDelay: $DTO->slowModeDelay,
            hasProtectedContent: $DTO->hasProtectedContent,
            stickerSetName: $DTO->stickerSetName,
            canSetStickerSet: $DTO->canSetStickerSet,
            linkedChatId: $DTO->linkedChatId,
            location: $DTO->location,
        );
    }

    public function mutate(UpdateChatMutateDTO $DTO): void
    {
        $this->type = $DTO->type;
        $this->title = $DTO->title;
        $this->username = $DTO->username;
        $this->firstName = $DTO->firstName;
        $this->lastName = $DTO->lastName;
        $this->photo = $DTO->photo;
        $this->bio = $DTO->bio;
        $this->hasPrivateForwards = $DTO->hasPrivateForwards;
        $this->description = $DTO->description;
        $this->inviteLink = $DTO->inviteLink;
        $this->pinnedMessage = $DTO->pinnedMessage;
        $this->permissions = $DTO->permissions;
        $this->slowModeDelay = $DTO->slowModeDelay;
        $this->hasProtectedContent = $DTO->hasProtectedContent;
        $this->stickerSetName = $DTO->stickerSetName;
        $this->canSetStickerSet = $DTO->canSetStickerSet;
        $this->linkedChatId = $DTO->linkedChatId;
        $this->location = $DTO->location;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSourceChatId(): int
    {
        return $this->sourceChatId;
    }

    public function getSourceServiceName(): string
    {
        return $this->sourceServiceName;
    }

    public function getType(): UpdateChatTypeEnum
    {
        return $this->type;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getPhoto(): ?ChatPhoto
    {
        return $this->photo;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function getHasPrivateForwards(): ?bool
    {
        return $this->hasPrivateForwards;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getInviteLink(): ?string
    {
        return $this->inviteLink;
    }

    public function getPinnedMessage(): ?UpdateMessage
    {
        return $this->pinnedMessage;
    }

    public function getPermissions(): ?ChatPermissions
    {
        return $this->permissions;
    }

    public function getSlowModeDelay(): ?int
    {
        return $this->slowModeDelay;
    }

    public function getHasProtectedContent(): ?bool
    {
        return $this->hasProtectedContent;
    }

    public function getStickerSetName(): ?string
    {
        return $this->stickerSetName;
    }

    public function getCanSetStickerSet(): ?bool
    {
        return $this->canSetStickerSet;
    }

    public function getLinkedChatId(): ?int
    {
        return $this->linkedChatId;
    }

    public function getLocation(): ?ChatLocation
    {
        return $this->location;
    }
}
