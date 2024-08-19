<?php

declare(strict_types=1);

namespace App\Main\Domain\DTO;

use App\Main\Domain\Entity\Update\Animation;
use App\Main\Domain\Entity\Update\Audio;
use App\Main\Domain\Entity\Update\Contact;
use App\Main\Domain\Entity\Update\Document;
use App\Main\Domain\Entity\Update\Invoice;
use App\Main\Domain\Entity\Update\Location;
use App\Main\Domain\Entity\Update\MessageEntity;
use App\Main\Domain\Entity\Update\PhotoSize;
use App\Main\Domain\Entity\Update\Poll;
use App\Main\Domain\Entity\Update\Sticker;
use App\Main\Domain\Entity\Update\SuccessfulPayment;
use App\Main\Domain\Entity\Update\UpdateChat;
use App\Main\Domain\Entity\Update\UpdateMessage;
use App\Main\Domain\Entity\Update\UpdateUser;
use App\Main\Domain\Entity\Update\Venue;
use App\Main\Domain\Entity\Update\Video;
use App\Main\Domain\Entity\Update\Voice;

final readonly class UpdateMessageMutateDTO
{
    /**
     * @param ?MessageEntity[] $entities
     * @param ?MessageEntity[] $captionEntities
     * @param ?PhotoSize[] $photo
     * @param ?UpdateUser[] $newChatMembers
     * @param ?PhotoSize[] $newChatPhoto
     */
    public function __construct(
        public ?UpdateUser $from,
        public int $date,
        public UpdateChat $chat,
        public ?UpdateUser $forwardFrom,
        public ?UpdateChat $forwardFromChat,
        public ?int $forwardFromMessageId,
        public ?string $forwardSignature,
        public ?string $forwardSenderName,
        public ?int $forwardDate,
        public ?UpdateMessage $replyToMessage,
        public ?int $editDate,
        public ?int $mediaGroupId,
        public ?string $authorSignature,
        public ?string $text,
        public ?array $entities,
        public ?array $captionEntities,
        public ?Audio $audio,
        public ?Document $document,
        public ?Animation $animation,
        public ?array $photo,
        public ?Sticker $sticker,
        public ?Video $video,
        public ?Voice $voice,
        public ?string $caption,
        public ?Contact $contact,
        public ?Location $location,
        public ?Venue $venue,
        public ?Poll $poll,
        public ?array $newChatMembers,
        public ?UpdateUser $leftChatMember,
        public ?string $newChatTitle,
        public ?array $newChatPhoto,
        public ?bool $deleteChatPhoto,
        public ?bool $groupChatCreated,
        public ?bool $supergroupChatCreated,
        public ?bool $channelChatCreated,
        public ?int $migrateToChatId,
        public ?int $migrateFromChatId,
        public ?UpdateMessage $pinnedMessage,
        public ?Invoice $invoice,
        public ?SuccessfulPayment $successfulPayment,
        public ?string $connectedWebsite,
        public ?string $replyMarkup,
    ) {}
}
