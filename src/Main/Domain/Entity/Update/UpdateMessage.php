<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Main\Domain\DTO\UpdateMessageMutateDTO;
use App\Shared\Domain\Service\UlidService;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_update_message')]
class UpdateMessage
{
    #[ORM\Id, ORM\Column(type: 'string', length: 26), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    #[ORM\Column]
    private readonly DateTimeImmutable $createdAt;

    /**
     * @param int $sourceMessageId
     * @param string $sourceServiceName
     * @param UpdateUser|null $from
     * @param int $date
     * @param UpdateChat $chat
     * @param UpdateUser|null $forwardFrom
     * @param UpdateChat|null $forwardFromChat
     * @param int|null $forwardFromMessageId
     * @param string|null $forwardSignature
     * @param string|null $forwardSenderName
     * @param int|null $forwardDate
     * @param UpdateMessage|null $replyToMessage
     * @param int|null $editDate
     * @param int|null $mediaGroupId
     * @param string|null $authorSignature
     * @param string|null $text
     * @param ?Collection<int, MessageEntity> $entities
     * @param ?Collection<int, MessageEntity> $captionEntities
     * @param Audio|null $audio
     * @param Document|null $document
     * @param Animation|null $animation
     * @param ?Collection<int, PhotoSize> $photo
     * @param Sticker|null $sticker
     * @param Video|null $video
     * @param Voice|null $voice
     * @param string|null $caption
     * @param Contact|null $contact
     * @param Location|null $location
     * @param Venue|null $venue
     * @param Poll|null $poll
     * @param ?Collection<int, UpdateUser> $newChatMembers
     * @param UpdateUser|null $leftChatMember
     * @param string|null $newChatTitle
     * @param ?Collection<int, PhotoSize> $newChatPhoto
     * @param bool|null $deleteChatPhoto
     * @param bool|null $groupChatCreated
     * @param bool|null $supergroupChatCreated
     * @param bool|null $channelChatCreated
     * @param int|null $migrateToChatId
     * @param int|null $migrateFromChatId
     * @param UpdateMessage|null $pinnedMessage
     * @param Invoice|null $invoice
     * @param SuccessfulPayment|null $successfulPayment
     * @param string|null $connectedWebsite
     * @param string|null $replyMarkup
     */
    public function __construct(
        #[ORM\Column(type: 'integer')]
        private readonly int $sourceMessageId,
        #[ORM\Column(type: 'string')]
        private readonly string $sourceServiceName,
        #[ORM\ManyToOne(targetEntity: UpdateUser::class)]
        #[ORM\JoinColumn(name: 'from_id', referencedColumnName: 'id')]
        private ?UpdateUser $from,
        #[ORM\Column(type: 'integer')]
        private int $date,
        #[ORM\ManyToOne(targetEntity: UpdateChat::class)]
        #[ORM\JoinColumn(name: 'chat_id', referencedColumnName: 'id')]
        private UpdateChat $chat,
        #[ORM\ManyToOne(targetEntity: UpdateUser::class)]
        #[ORM\JoinColumn(name: 'forward_from_id', referencedColumnName: 'id')]
        private ?UpdateUser $forwardFrom,
        #[ORM\ManyToOne(targetEntity: UpdateChat::class)]
        #[ORM\JoinColumn(name: 'forward_from_chat_id', referencedColumnName: 'id')]
        private ?UpdateChat $forwardFromChat,
        #[ORM\Column(type: 'integer', nullable: true)]
        private ?int $forwardFromMessageId,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $forwardSignature,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $forwardSenderName,
        #[ORM\Column(type: 'integer', nullable: true)]
        private ?int $forwardDate,
        #[ORM\ManyToOne(targetEntity: self::class)]
        #[ORM\JoinColumn(name: 'reply_to_message_id', referencedColumnName: 'id')]
        private ?self $replyToMessage,
        #[ORM\Column(type: 'integer', nullable: true)]
        private ?int $editDate,
        #[ORM\Column(type: 'bigint', nullable: true)]
        private ?int $mediaGroupId,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $authorSignature,
        #[ORM\Column(type: 'text', nullable: true)]
        private ?string $text,
        #[ORM\JoinTable(name: 'updates_update_message_messages_entities')]
        #[ORM\JoinColumn(name: 'message_id', referencedColumnName: 'id')]
        #[ORM\InverseJoinColumn(name: 'entity_id', referencedColumnName: 'id', unique: true)]
        #[ORM\ManyToMany(targetEntity: MessageEntity::class)]
        private ?Collection $entities,
        #[ORM\JoinTable(name: 'updates_update_message_messages_caption_entities')]
        #[ORM\JoinColumn(name: 'message_id', referencedColumnName: 'id')]
        #[ORM\InverseJoinColumn(name: 'entity_id', referencedColumnName: 'id', unique: true)]
        #[ORM\ManyToMany(targetEntity: MessageEntity::class)]
        private ?Collection $captionEntities,
        #[ORM\OneToOne(targetEntity: Audio::class)]
        #[ORM\JoinColumn(name: 'audio_id', referencedColumnName: 'id')]
        private ?Audio $audio,
        #[ORM\OneToOne(targetEntity: Document::class)]
        #[ORM\JoinColumn(name: 'document_id', referencedColumnName: 'id')]
        private ?Document $document,
        #[ORM\OneToOne(targetEntity: Animation::class)]
        #[ORM\JoinColumn(name: 'animation_id', referencedColumnName: 'id')]
        private ?Animation $animation,
        #[ORM\JoinTable(name: 'updates_update_message_messages_photos')]
        #[ORM\JoinColumn(name: 'message_id', referencedColumnName: 'id')]
        #[ORM\InverseJoinColumn(name: 'photo_id', referencedColumnName: 'id', unique: true)]
        #[ORM\ManyToMany(targetEntity: PhotoSize::class)]
        private ?Collection $photo,
        #[ORM\OneToOne(targetEntity: Sticker::class)]
        #[ORM\JoinColumn(name: 'sticker_id', referencedColumnName: 'id')]
        private ?Sticker $sticker,
        #[ORM\OneToOne(targetEntity: Video::class)]
        #[ORM\JoinColumn(name: 'video_id', referencedColumnName: 'id')]
        private ?Video $video,
        #[ORM\OneToOne(targetEntity: Voice::class)]
        #[ORM\JoinColumn(name: 'voice_id', referencedColumnName: 'id')]
        private ?Voice $voice,
        #[ORM\Column(type: 'text', nullable: true)]
        private ?string $caption,
        #[ORM\OneToOne(targetEntity: Contact::class)]
        #[ORM\JoinColumn(name: 'contact_id', referencedColumnName: 'id')]
        private ?Contact $contact,
        #[ORM\OneToOne(targetEntity: Location::class)]
        #[ORM\JoinColumn(name: 'location_id', referencedColumnName: 'id')]
        private ?Location $location,
        #[ORM\OneToOne(targetEntity: Venue::class)]
        #[ORM\JoinColumn(name: 'venue_id', referencedColumnName: 'id')]
        private ?Venue $venue,
        #[ORM\OneToOne(targetEntity: Poll::class)]
        #[ORM\JoinColumn(name: 'poll_id', referencedColumnName: 'id')]
        private ?Poll $poll,
        #[ORM\JoinTable(name: 'updates_update_message_messages_new_chat_members')]
        #[ORM\JoinColumn(name: 'message_id', referencedColumnName: 'id', unique: true)]
        #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'id')]
        #[ORM\ManyToMany(targetEntity: UpdateUser::class)]
        private ?Collection $newChatMembers,
        #[ORM\OneToOne(targetEntity: UpdateUser::class)]
        #[ORM\JoinColumn(name: 'left_chat_member_id', referencedColumnName: 'id')]
        private ?UpdateUser $leftChatMember,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $newChatTitle,
        #[ORM\JoinTable(name: 'updates_update_message_messages_new_chat_photos')]
        #[ORM\JoinColumn(name: 'message_id', referencedColumnName: 'id')]
        #[ORM\InverseJoinColumn(name: 'photo_id', referencedColumnName: 'id', unique: true)]
        #[ORM\ManyToMany(targetEntity: PhotoSize::class)]
        private ?Collection $newChatPhoto,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $deleteChatPhoto,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $groupChatCreated,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $supergroupChatCreated,
        #[ORM\Column(type: 'boolean', nullable: true)]
        private ?bool $channelChatCreated,
        #[ORM\Column(type: 'bigint', nullable: true)]
        private ?int $migrateToChatId,
        #[ORM\Column(type: 'bigint', nullable: true)]
        private ?int $migrateFromChatId,
        #[ORM\OneToOne(targetEntity: self::class)]
        #[ORM\JoinColumn(name: 'pinned_message_id', referencedColumnName: 'id')]
        private ?self $pinnedMessage,
        #[ORM\OneToOne(targetEntity: Invoice::class)]
        #[ORM\JoinColumn(name: 'invoice_id', referencedColumnName: 'id')]
        private ?Invoice $invoice,
        #[ORM\OneToOne(targetEntity: SuccessfulPayment::class)]
        #[ORM\JoinColumn(name: 'successful_payment_id', referencedColumnName: 'id')]
        private ?SuccessfulPayment $successfulPayment,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $connectedWebsite,
        #[ORM\Column(type: 'text', nullable: true)]
        private ?string $replyMarkup,
    ) {
        $this->id = UlidService::generate();
        $this->createdAt = new DateTimeImmutable();
    }

    public static function createFromMutateDTO(
        int $sourceMessageId,
        string $sourceServiceName,
        UpdateMessageMutateDTO $DTO
    ): self {
        return new self(
            sourceMessageId: $sourceMessageId,
            sourceServiceName: $sourceServiceName,
            from: $DTO->from,
            date: $DTO->date,
            chat: $DTO->chat,
            forwardFrom: $DTO->forwardFrom,
            forwardFromChat: $DTO->forwardFromChat,
            forwardFromMessageId: $DTO->forwardFromMessageId,
            forwardSignature: $DTO->forwardSignature,
            forwardSenderName: $DTO->forwardSenderName,
            forwardDate: $DTO->forwardDate,
            replyToMessage: $DTO->replyToMessage,
            editDate: $DTO->editDate,
            mediaGroupId: $DTO->mediaGroupId,
            authorSignature: $DTO->authorSignature,
            text: $DTO->text,
            entities: new ArrayCollection($DTO->entities ?? []),
            captionEntities: new ArrayCollection($DTO->captionEntities ?? []),
            audio: $DTO->audio,
            document: $DTO->document,
            animation: $DTO->animation,
            photo: new ArrayCollection($DTO->photo ?? []),
            sticker: $DTO->sticker,
            video: $DTO->video,
            voice: $DTO->voice,
            caption: $DTO->caption,
            contact: $DTO->contact,
            location: $DTO->location,
            venue: $DTO->venue,
            poll: $DTO->poll,
            newChatMembers: new ArrayCollection($DTO->newChatMembers ?? []),
            leftChatMember: $DTO->leftChatMember,
            newChatTitle: $DTO->newChatTitle,
            newChatPhoto: new ArrayCollection($DTO->newChatPhoto ?? []),
            deleteChatPhoto: $DTO->deleteChatPhoto,
            groupChatCreated: $DTO->groupChatCreated,
            supergroupChatCreated: $DTO->supergroupChatCreated,
            channelChatCreated: $DTO->channelChatCreated,
            migrateToChatId: $DTO->migrateToChatId,
            migrateFromChatId: $DTO->migrateFromChatId,
            pinnedMessage: $DTO->pinnedMessage,
            invoice: $DTO->invoice,
            successfulPayment: $DTO->successfulPayment,
            connectedWebsite: $DTO->connectedWebsite,
            replyMarkup: $DTO->replyMarkup,
        );
    }

    public function mutate(UpdateMessageMutateDTO $DTO): void
    {
        $this->from = $DTO->from;
        $this->date = $DTO->date;
        $this->chat = $DTO->chat;
        $this->forwardFrom = $DTO->forwardFrom;
        $this->forwardFromChat = $DTO->forwardFromChat;
        $this->forwardFromMessageId = $DTO->forwardFromMessageId;
        $this->forwardSignature = $DTO->forwardSignature;
        $this->forwardSenderName = $DTO->forwardSenderName;
        $this->forwardDate = $DTO->forwardDate;
        $this->replyToMessage = $DTO->replyToMessage;
        $this->editDate = $DTO->editDate;
        $this->mediaGroupId = $DTO->mediaGroupId;
        $this->authorSignature = $DTO->authorSignature;
        $this->text = $DTO->text;
        $this->entities = new ArrayCollection($DTO->entities ?? []);
        $this->captionEntities = new ArrayCollection(
            $DTO->captionEntities ?? []
        );
        $this->audio = $DTO->audio;
        $this->document = $DTO->document;
        $this->animation = $DTO->animation;
        $this->photo = new ArrayCollection($DTO->photo ?? []);
        $this->sticker = $DTO->sticker;
        $this->video = $DTO->video;
        $this->voice = $DTO->voice;
        $this->caption = $DTO->caption;
        $this->contact = $DTO->contact;
        $this->location = $DTO->location;
        $this->venue = $DTO->venue;
        $this->poll = $DTO->poll;
        $this->newChatMembers = new ArrayCollection($DTO->newChatMembers ?? []);
        $this->leftChatMember = $DTO->leftChatMember;
        $this->newChatTitle = $DTO->newChatTitle;
        $this->newChatPhoto = new ArrayCollection($DTO->newChatPhoto ?? []);
        $this->deleteChatPhoto = $DTO->deleteChatPhoto;
        $this->groupChatCreated = $DTO->groupChatCreated;
        $this->supergroupChatCreated = $DTO->supergroupChatCreated;
        $this->channelChatCreated = $DTO->channelChatCreated;
        $this->migrateToChatId = $DTO->migrateToChatId;
        $this->migrateFromChatId = $DTO->migrateFromChatId;
        $this->pinnedMessage = $DTO->pinnedMessage;
        $this->invoice = $DTO->invoice;
        $this->successfulPayment = $DTO->successfulPayment;
        $this->connectedWebsite = $DTO->connectedWebsite;
        $this->replyMarkup = $DTO->replyMarkup;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getSourceMessageId(): int
    {
        return $this->sourceMessageId;
    }

    public function getSourceServiceName(): string
    {
        return $this->sourceServiceName;
    }

    public function getFrom(): ?UpdateUser
    {
        return $this->from;
    }

    public function getDate(): int
    {
        return $this->date;
    }

    public function getChat(): UpdateChat
    {
        return $this->chat;
    }

    public function getForwardFrom(): ?UpdateUser
    {
        return $this->forwardFrom;
    }

    public function getForwardFromChat(): ?UpdateChat
    {
        return $this->forwardFromChat;
    }

    public function getForwardFromMessageId(): ?int
    {
        return $this->forwardFromMessageId;
    }

    public function getForwardSignature(): ?string
    {
        return $this->forwardSignature;
    }

    public function getForwardSenderName(): ?string
    {
        return $this->forwardSenderName;
    }

    public function getForwardDate(): ?int
    {
        return $this->forwardDate;
    }

    public function getReplyToMessage(): ?self
    {
        return $this->replyToMessage;
    }

    public function getEditDate(): ?int
    {
        return $this->editDate;
    }

    public function getMediaGroupId(): ?int
    {
        return $this->mediaGroupId;
    }

    public function getAuthorSignature(): ?string
    {
        return $this->authorSignature;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    /** @return ?MessageEntity[] */
    public function getEntities(): ?array
    {
        /** @var ?MessageEntity[] $result */
        $result = $this->entities?->toArray();

        return $result;
    }

    /** @return ?MessageEntity[] */
    public function getCaptionEntities(): ?array
    {
        /** @var ?MessageEntity[] $result */
        $result = $this->captionEntities?->toArray();

        return $result;
    }

    public function getAudio(): ?Audio
    {
        return $this->audio;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function getAnimation(): ?Animation
    {
        return $this->animation;
    }

    /** @return ?PhotoSize[] */
    public function getPhoto(): ?array
    {
        /** @var ?PhotoSize[] $result */
        $result = $this->photo?->toArray();

        return $result;
    }

    public function getSticker(): ?Sticker
    {
        return $this->sticker;
    }

    public function getVideo(): ?Video
    {
        return $this->video;
    }

    public function getVoice(): ?Voice
    {
        return $this->voice;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function getVenue(): ?Venue
    {
        return $this->venue;
    }

    public function getPoll(): ?Poll
    {
        return $this->poll;
    }

    /** @return ?UpdateUser[] */
    public function getNewChatMembers(): ?array
    {
        /** @var ?UpdateUser[] $result */
        $result = $this->newChatMembers?->toArray();

        return $result;
    }

    public function getLeftChatMember(): ?UpdateUser
    {
        return $this->leftChatMember;
    }

    public function getNewChatTitle(): ?string
    {
        return $this->newChatTitle;
    }

    /** @return ?PhotoSize[] */
    public function getNewChatPhoto(): ?array
    {
        /** @var ?PhotoSize[] $result */
        $result = $this->newChatPhoto?->toArray();

        return $result;
    }

    public function getDeleteChatPhoto(): ?bool
    {
        return $this->deleteChatPhoto;
    }

    public function getGroupChatCreated(): ?bool
    {
        return $this->groupChatCreated;
    }

    public function getSupergroupChatCreated(): ?bool
    {
        return $this->supergroupChatCreated;
    }

    public function getChannelChatCreated(): ?bool
    {
        return $this->channelChatCreated;
    }

    public function getMigrateToChatId(): ?int
    {
        return $this->migrateToChatId;
    }

    public function getMigrateFromChatId(): ?int
    {
        return $this->migrateFromChatId;
    }

    public function getPinnedMessage(): ?self
    {
        return $this->pinnedMessage;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function getSuccessfulPayment(): ?SuccessfulPayment
    {
        return $this->successfulPayment;
    }

    public function getConnectedWebsite(): ?string
    {
        return $this->connectedWebsite;
    }

    public function getReplyMarkup(): ?string
    {
        return $this->replyMarkup;
    }

    public function getChatSourceChatId(): int
    {
        return $this->chat->getSourceChatId();
    }

    public function setFrom(?UpdateUser $user): void
    {
        $this->from = $user;
    }
}
