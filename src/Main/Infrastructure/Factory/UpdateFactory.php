<?php

declare(strict_types=1);

namespace App\Main\Infrastructure\Factory;

use App\Main\Domain\DTO\UpdateChatMutateDTO;
use App\Main\Domain\DTO\UpdateMessageMutateDTO;
use App\Main\Domain\DTO\UpdateSourceDTO;
use App\Main\Domain\DTO\UpdateUserMutateDTO;
use App\Main\Domain\Entity\Update\Animation;
use App\Main\Domain\Entity\Update\Audio;
use App\Main\Domain\Entity\Update\CallbackQuery;
use App\Main\Domain\Entity\Update\ChatLocation;
use App\Main\Domain\Entity\Update\ChatPermissions;
use App\Main\Domain\Entity\Update\ChatPhoto;
use App\Main\Domain\Entity\Update\Contact;
use App\Main\Domain\Entity\Update\Document;
use App\Main\Domain\Entity\Update\Invoice;
use App\Main\Domain\Entity\Update\Location;
use App\Main\Domain\Entity\Update\MaskPosition;
use App\Main\Domain\Entity\Update\MessageEntity;
use App\Main\Domain\Entity\Update\OrderInfo;
use App\Main\Domain\Entity\Update\PhotoSize;
use App\Main\Domain\Entity\Update\Poll;
use App\Main\Domain\Entity\Update\PollOption;
use App\Main\Domain\Entity\Update\ShippingAddress;
use App\Main\Domain\Entity\Update\Sticker;
use App\Main\Domain\Entity\Update\SuccessfulPayment;
use App\Main\Domain\Entity\Update\Update;
use App\Main\Domain\Entity\Update\UpdateChat;
use App\Main\Domain\Entity\Update\UpdateMessage;
use App\Main\Domain\Entity\Update\UpdateUser;
use App\Main\Domain\Entity\Update\Venue;
use App\Main\Domain\Entity\Update\Video;
use App\Main\Domain\Entity\Update\Voice;
use App\Main\Domain\Enum\UpdateChatTypeEnum;
use App\Main\Domain\Exception\ChatClientAPI\GetUpdatesException;
use App\Main\Domain\Exception\UpdateEntities\UpdateChatNotFoundException;
use App\Main\Domain\Exception\UpdateEntities\UpdateMessageNotFoundException;
use App\Main\Domain\Exception\UpdateEntities\UpdateUserNotFoundException;
use App\Main\Domain\Repository\UpdateRepositoryInterface;
use App\Main\Infrastructure\ChatClientAPI\Telegram\TelegramClient;
use Doctrine\Common\Collections\ArrayCollection;
use TelegramBot\Api\Types as TG;

final readonly class UpdateFactory
{
    public function __construct(
        private UpdateRepositoryInterface $updateRepository,
    ) {}

    /** @throws GetUpdatesException */
    public function createUpdateFromTelegram(
        TG\Update $update,
        string $botId
    ): ?Update {
        $message = $this
            ->createOrGetAndUpdateMessageFromTelegramResponse(
                $update->getMessage()
            );
        $chat = $this
            ->createOrGetAndUpdateChatFromTelegramResponseNullable(
                $update->getMessage()?->getChat()
            );

        $callbackQueryTG = $update->getCallbackQuery();
        $callbackQuery = null;
        if (!$message && $callbackQueryTG) {
            $message = $this
                ->createOrGetAndUpdateMessageFromTelegramResponse(
                    $callbackQueryTG->getMessage()
                );

            // todo Rewrite to disable flush unneeded bot user row
            // In callback message.from is bot, not user -> need force change it
            $message?->setFrom(
                $this->createUpdateUserFromTelegramResponseNullable(
                    $callbackQueryTG->getFrom()
                )
            );
            $chat = $this
                ->createOrGetAndUpdateChatFromTelegramResponseNullable(
                    $callbackQueryTG->getMessage()?->getChat()
                );

            $callbackQuery = $this
                ->createOrGetAndUpdateCallbackQueryFromTelegramResponse(
                    $callbackQueryTG
                );
        }

        return $this->createUpdateIfNotExist(
            id: $update->getUpdateId(),
            source: TelegramClient::getClientAdapterName(),
            botId: $botId,
            chat: $chat,
            message: $message,
            callbackQuery: $callbackQuery,
        );
    }

    /** @throws GetUpdatesException */
    public function createOrGetAndUpdateMessageFromTelegramResponse(
        ?TG\Message $m
    ): ?UpdateMessage {
        if (null === $m) {
            return null;
        }

        $mutateDTO = new UpdateMessageMutateDTO(
            from: $this->createUpdateUserFromTelegramResponseNullable(
                $m->getFrom()
            ),
            date: $m->getDate(),
            chat: $this->createOrGetAndUpdateChatFromTelegramResponse(
                $m->getChat()
            ),
            forwardFrom: $this->createUpdateUserFromTelegramResponseNullable(
                $m->getForwardFrom()
            ),
            forwardFromChat: $this->createOrGetAndUpdateChatFromTelegramResponseNullable(
                $m->getForwardFromChat()
            ),
            forwardFromMessageId: $m->getForwardFromMessageId(),
            forwardSignature: $m->getForwardSignature(),
            forwardSenderName: $m->getForwardSenderName(),
            forwardDate: $m->getForwardDate(),
            replyToMessage: $this->createOrGetAndUpdateMessageFromTelegramResponse(
                $m->getReplyToMessage()
            ),
            editDate: $m->getEditDate(),
            mediaGroupId: $m->getMediaGroupId(
            ) !== null ? (int)$m->getMediaGroupId() : null,
            authorSignature: $m->getAuthorSignature(),
            text: $m->getText(),
            entities: $this->createMessageEntityFromTelegramResponseCollection(
                $m->getEntities()
            ),
            captionEntities: $this->createMessageEntityFromTelegramResponseCollection(
                $m->getCaptionEntities()
            ),
            audio: $this->createAudioFromTelegramResponse($m->getAudio()),
            document: $this->createDocumentFromTelegramResponse(
                $m->getDocument()
            ),
            animation: $this->createAnimationFromTelegramResponse(
                $m->getAnimation()
            ),
            photo: $this->createPhotoSizeFromTelegramResponseCollection(
            $m->getPhoto()
        ) ?? [],
            sticker: $this->createStickerFromTelegramResponseNullable(
                $m->getSticker()
            ),
            video: $this->createVideoFromTelegramResponse($m->getVideo()),
            voice: $this->createVoiceFromTelegramResponse($m->getVoice()),
            caption: $m->getCaption(),
            contact: $this->createContactFromTelegramResponse($m->getContact()),
            location: $this->createLocationFromTelegramResponseNullable(
                $m->getLocation()
            ),
            venue: $this->createVenueFromTelegramResponse($m->getVenue()),
            poll: $this->createPollFromTelegramResponse($m->getPoll()),
            newChatMembers: $this->createUserFromTelegramResponseCollection(
            $m->getNewChatMembers()
        ) ?? [],
            leftChatMember: $this->createUpdateUserFromTelegramResponseNullable(
                $m->getLeftChatMember()
            ),
            newChatTitle: $m->getNewChatTitle(),
            newChatPhoto: $this->createPhotoSizeFromTelegramResponseCollection(
            $m->getNewChatPhoto()
        ) ?? [],
            deleteChatPhoto: $m->isDeleteChatPhoto(),
            groupChatCreated: $m->isGroupChatCreated(),
            supergroupChatCreated: $m->isSupergroupChatCreated(),
            channelChatCreated: $m->isChannelChatCreated(),
            migrateToChatId: $m->getMigrateToChatId(),
            migrateFromChatId: $m->getMigrateFromChatId(),
            pinnedMessage: $this->createOrGetAndUpdateMessageFromTelegramResponse(
                $m->getPinnedMessage()
            ),
            invoice: $this->createInvoiceFromTelegramResponse($m->getInvoice()),
            successfulPayment: $this->createSuccessfulPaymentFromTelegramResponse(
                $m->getSuccessfulPayment()
            ),
            connectedWebsite: $m->getConnectedWebsite(),
            replyMarkup: (string)json_encode(
                $m->getReplyMarkup() ?? []
            ), // TODO Я не знаю что с этим делать
        );

        try {
            $entity = $this->updateRepository->getMessageBySourceDTO(
                new UpdateSourceDTO(
                    (int)$m->getMessageId(),
                    TelegramClient::getClientAdapterName()
                )
            );
            $entity->mutate($mutateDTO);
        } catch (UpdateMessageNotFoundException) {
            $entity = UpdateMessage::createFromMutateDTO(
                (int)$m->getMessageId(),
                TelegramClient::getClientAdapterName(),
                $mutateDTO
            );
            $this->updateRepository->add($entity);
        }

        return $entity;
    }

    public function createUpdateUserFromTelegramResponseNullable(
        ?TG\User $u
    ): ?UpdateUser {
        if (null === $u) {
            return null;
        }

        return $this->createUpdateUserFromTelegramResponse($u);
    }

    public function createUpdateUserFromTelegramResponse(TG\User $u): UpdateUser
    {
        $mutateDTO = new UpdateUserMutateDTO(
            $u->isBot(),
            $u->getFirstName(),
            $u->getLastName(),
            $u->getUsername(),
            $u->getLanguageCode(),
            $u->getCanJoinGroups(),
            $u->getCanReadAllGroupMessages(),
        );

        try {
            $entity = $this->updateRepository->getUserBySourceDTO(
                new UpdateSourceDTO(
                    (int)$u->getId(),
                    TelegramClient::getClientAdapterName()
                )
            );
            $entity->mutate($mutateDTO);
        } catch (UpdateUserNotFoundException) {
            $entity = UpdateUser::createFromMutateDTO(
                (int)$u->getId(),
                TelegramClient::getClientAdapterName(),
                $mutateDTO
            );
            $this->updateRepository->add($entity, true);
        }

        return $entity;
    }

    /** @throws GetUpdatesException */
    public function createOrGetAndUpdateChatFromTelegramResponse(
        TG\Chat $c
    ): UpdateChat {
        $type = match ($c->getType()) {
            'private' => UpdateChatTypeEnum::PRIVATE,
            'supergroup', 'group' => UpdateChatTypeEnum::GROUP,
            'channel' => UpdateChatTypeEnum::CHANNEL,
            default => throw new GetUpdatesException(
                'Unknown chat type'
            ),
        };

        $pinnedMessage = null !== $c->getPinnedMessage() ?
            $this->createOrGetAndUpdateMessageFromTelegramResponse(
                $c->getPinnedMessage()
            ) :
            null;

        $mutateDTO = new UpdateChatMutateDTO(
            type: $type,
            title: $c->getTitle(),
            username: $c->getUsername(),
            firstName: $c->getFirstName(),
            lastName: $c->getLastName(),
            photo: $this->createChatPhotoFromTelegramResponseBase(
                $c->getPhoto()
            ),
            bio: $c->getBio(),
            hasPrivateForwards: $c->getHasPrivateForwards(),
            description: $c->getDescription(),
            inviteLink: $c->getInviteLink(),
            pinnedMessage: $pinnedMessage,
            permissions: $this->createChatPermissionsFromTelegramResponse(
                $c->getPermissions()
            ),
            slowModeDelay: $c->getSlowModeDelay(),
            hasProtectedContent: $c->getHasProtectedContent(),
            stickerSetName: $c->getStickerSetName(),
            canSetStickerSet: $c->getCanSetStickerSet(),
            linkedChatId: $c->getLinkedChatId(),
            location: $this->createChatLocationFromTelegramResponse(
                $c->getLocation()
            ),
        );

        try {
            $entity = $this->updateRepository
                ->getChatBySourceDTO(
                    new UpdateSourceDTO(
                        (int)$c->getId(),
                        TelegramClient::getClientAdapterName(),
                    )
                );
            $entity->mutate($mutateDTO);
        } catch (UpdateChatNotFoundException) {
            $entity = UpdateChat::createFromMutateDTO(
                sourceChatId: (int)$c->getId(),
                sourceServiceName: TelegramClient::getClientAdapterName(),
                DTO: $mutateDTO
            );
            $this->updateRepository->add($entity, true);
        }

        return $entity;
    }

    private function createChatPhotoFromTelegramResponseBase(
        ?TG\ChatPhoto $media
    ): ?ChatPhoto {
        if (null === $media) {
            return null;
        }

        $entity = new ChatPhoto(
            $media->getSmallFileId(),
            $media->getBigFileId(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    private function createChatPermissionsFromTelegramResponse(
        ?TG\ChatPermissions $p
    ): ?ChatPermissions {
        if (null === $p) {
            return null;
        }

        $entity = new ChatPermissions(
            $p->isCanSendMessages(),
            $p->isCanSendMediaMessages(),
            $p->isCanSendPolls(),
            $p->isCanSendOtherMessages(),
            $p->isCanAddWebPagePreviews(),
            $p->isCanChangeInfo(),
            $p->isCanInviteUsers(),
            $p->isCanPinMessages(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    private function createChatLocationFromTelegramResponse(
        ?TG\ChatLocation $l
    ): ?ChatLocation {
        if (null === $l) {
            return null;
        }

        $entity = new ChatLocation(
            $this->createLocationFromTelegramResponse($l->getLocation()),
            $l->getAddress(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    private function createLocationFromTelegramResponse(
        TG\Location $l
    ): Location {
        $entity = new Location(
            $l->getLongitude(),
            $l->getLatitude(),
            $l->getHorizontalAccuracy(),
            $l->getLivePeriod(),
            $l->getHeading(),
            $l->getProximityAlertRadius(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    /** @throws GetUpdatesException */
    public function createOrGetAndUpdateChatFromTelegramResponseNullable(
        ?TG\Chat $c
    ): ?UpdateChat {
        if (null === $c) {
            return null;
        }

        return $this->createOrGetAndUpdateChatFromTelegramResponse($c);
    }

    /**
     * @return MessageEntity[]
     */
    private function createMessageEntityFromTelegramResponseCollection(
        mixed $e
    ): array {
        if (null === $e) {
            return [];
        }

        $messageEntityDTOs = [];

        if (is_iterable($e)) {
            /** @var TG\MessageEntity $messageEntity */
            foreach ($e as $messageEntity) {
                $messageEntityDTOs[] = $this->createMessageEntityFromTelegramResponse(
                    $messageEntity
                );
            }
        }

        return $messageEntityDTOs;
    }

    private function createMessageEntityFromTelegramResponse(
        TG\MessageEntity $e
    ): MessageEntity {
        $entity = new MessageEntity(
            $e->getType(),
            $e->getOffset(),
            $e->getLength(),
            $e->getUrl(),
            $this->createUpdateUserFromTelegramResponseNullable(
                $e->getUser()
            ),
            $e->getLanguage(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    private function createAudioFromTelegramResponse(?TG\Audio $a): ?Audio
    {
        if (null === $a) {
            return null;
        }

        $entity = new Audio(
            $a->getFileId(),
            $a->getFileUniqueId(),
            $a->getDuration(),
            $a->getPerformer(),
            $a->getTitle(),
            $a->getMimeType(),
            $a->getFileSize(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    private function createDocumentFromTelegramResponse(
        ?TG\Document $d
    ): ?Document {
        if (null === $d) {
            return null;
        }

        $entity = new Document(
            $d->getFileId(),
            $d->getFileUniqueId(),
            $this->createPhotoSizeFromTelegramResponseNullable(
                $d->getThumbnail()
            ),
            $d->getFileName(),
            $d->getMimeType(),
            $d->getFileSize(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    private function createPhotoSizeFromTelegramResponseNullable(
        ?TG\PhotoSize $p
    ): ?PhotoSize {
        if (null === $p) {
            return null;
        }

        return $this->createPhotoSizeFromTelegramResponse($p);
    }

    private function createPhotoSizeFromTelegramResponse(
        TG\PhotoSize $p
    ): PhotoSize {
        $entity = new PhotoSize(
            $p->getFileId(),
            $p->getFileUniqueId(),
            $p->getWidth(),
            $p->getHeight(),
            $p->getFileSize(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    private function createAnimationFromTelegramResponse(
        ?TG\Animation $a
    ): ?Animation {
        if (null === $a) {
            return null;
        }

        $entity = new Animation(
            $a->getFileId(),
            $a->getFileUniqueId(),
            $a->getWidth(),
            $a->getHeight(),
            $a->getDuration(),
            $this->createPhotoSizeFromTelegramResponseNullable(
                $a->getThumbnail()
            ),
            $a->getFileName(),
            $a->getMimeType(),
            $a->getFileSize(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    /** @return ?PhotoSize[] */
    private function createPhotoSizeFromTelegramResponseCollection(
        mixed $photoSizes
    ): ?array {
        if (null === $photoSizes) {
            return null;
        }

        $photoSizeDTOs = [];

        if (is_iterable($photoSizes)) {
            /** @var TG\PhotoSize $photoSize */
            foreach ($photoSizes as $photoSize) {
                $photoSizeDTOs[] = $this->createPhotoSizeFromTelegramResponse(
                    $photoSize
                );
            }
        }

        return $photoSizeDTOs;
    }

    private function createStickerFromTelegramResponseNullable(
        ?TG\Sticker $s
    ): ?Sticker {
        if (null === $s) {
            return null;
        }

        return $this->createStickerFromTelegramResponse($s);
    }

    private function createStickerFromTelegramResponse(TG\Sticker $s): Sticker
    {
        $entity = new Sticker(
            $s->getFileId(),
            $s->getFileUniqueId(),
            $s->getWidth(),
            $s->getHeight(),
            $s->getIsAnimated(),
            $this->createPhotoSizeFromTelegramResponseNullable(
                $s->getThumbnail()
            ),
            $s->getEmoji(),
            $s->getSetName(),
            $this->createMaskPositionFromTelegramResponse(
                $s->getMaskPosition()
            ),
            $s->getFileSize(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    private function createMaskPositionFromTelegramResponse(
        ?TG\MaskPosition $m
    ): ?MaskPosition {
        if (null === $m) {
            return null;
        }

        $entity = new MaskPosition(
            $m->getPoint(),
            $m->getXShift(),
            $m->getYShift(),
            $m->getScale(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    private function createVideoFromTelegramResponse(?TG\Video $v): ?Video
    {
        if (null === $v) {
            return null;
        }

        $entity = new Video(
            $v->getFileId(),
            $v->getFileUniqueId(),
            $v->getWidth(),
            $v->getHeight(),
            $v->getDuration(),
            $this->createPhotoSizeFromTelegramResponseNullable(
                $v->getThumbnail()
            ),
            $v->getMimeType(),
            $v->getFileSize(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    private function createVoiceFromTelegramResponse(?TG\Voice $v): ?Voice
    {
        if (null === $v) {
            return null;
        }

        $entity = new Voice(
            $v->getFileId(),
            $v->getFileUniqueId(),
            $v->getDuration(),
            $v->getMimeType(),
            $v->getFileSize(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    private function createContactFromTelegramResponse(?TG\Contact $c): ?Contact
    {
        if (null === $c) {
            return null;
        }

        $entity = new Contact(
            $c->getPhoneNumber(),
            $c->getFirstName(),
            $c->getLastName(),
            $c->getUserId(),
            $c->getVcard(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    private function createLocationFromTelegramResponseNullable(
        ?TG\Location $l
    ): ?Location {
        if (null === $l) {
            return null;
        }

        return $this->createLocationFromTelegramResponse($l);
    }

    private function createVenueFromTelegramResponse(?TG\Venue $v): ?Venue
    {
        if (null === $v) {
            return null;
        }

        $entity = new Venue(
            $this->createLocationFromTelegramResponse($v->getLocation()),
            $v->getTitle(),
            $v->getAddress(),
            $v->getFoursquareId(),
            $v->getFoursquareType(),
            $v->getGooglePlaceId(),
            $v->getGooglePlaceType(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    private function createPollFromTelegramResponse(?TG\Poll $p): ?Poll
    {
        if (null === $p) {
            return null;
        }

        $entity = new Poll(
            $p->getId(),
            $p->getQuestion(),
            new ArrayCollection(
                $this->createPollOptionFromTelegramResponseCollection(
                    $p->getOptions()
                ) ?? []
            ),
            $p->getTotalVoterCount(),
            $p->isClosed(),
            $p->isAnonymous(),
            $p->getType(),
            $p->isAllowsMultipleAnswers(),
            $p->getCorrectOptionId(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    /**
     * @param array<int, TG\PollOption>|null $pollOptions
     *
     * @return ?PollOption[]
     */
    private function createPollOptionFromTelegramResponseCollection(
        ?array $pollOptions
    ): ?array {
        if (null === $pollOptions) {
            return null;
        }

        $pollOptionDTOs = [];

        foreach ($pollOptions as $pollOption) {
            $pollOptionDTOs[] = $this->createPollOptionFromTelegramResponse(
                $pollOption
            );
        }

        return $pollOptionDTOs;
    }

    private function createPollOptionFromTelegramResponse(
        TG\PollOption $o
    ): PollOption {
        $entity = new PollOption(
            $o->getText(),
            $o->getVoterCount(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    /**
     * @param array<int, TG\User>|null $newChatMembers
     *
     * @return ?UpdateUser[]
     */
    private function createUserFromTelegramResponseCollection(
        mixed $newChatMembers
    ): ?array {
        if (null === $newChatMembers) {
            return null;
        }

        $userDTOs = [];

        foreach ($newChatMembers as $newChatMember) {
            $userDTOs[] = $this->createUpdateUserFromTelegramResponse(
                $newChatMember
            );
        }

        return $userDTOs;
    }

    private function createInvoiceFromTelegramResponse(
        ?TG\Payments\Invoice $i
    ): ?Invoice {
        if (null === $i) {
            return null;
        }

        $entity = new Invoice(
            $i->getTitle(),
            $i->getDescription(),
            $i->getStartParameter(),
            $i->getCurrency(),
            $i->getTotalAmount(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    private function createSuccessfulPaymentFromTelegramResponse(
        ?TG\Payments\SuccessfulPayment $s
    ): ?SuccessfulPayment {
        if (null === $s) {
            return null;
        }

        /** @var string $providerPaymentChargeId */
        $providerPaymentChargeId = $s->getProviderPaymentChargeId();

        $entity = new SuccessfulPayment(
            $s->getCurrency(),
            $s->getTotalAmount(),
            $s->getInvoicePayload(),
            $s->getShippingOptionId(),
            $this->createOrderInfoFromTelegramResponse($s->getOrderInfo()),
            $s->getTelegramPaymentChargeId(),
            $providerPaymentChargeId,
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    private function createOrderInfoFromTelegramResponse(
        ?TG\Payments\OrderInfo $s
    ): ?OrderInfo {
        if (null === $s) {
            return null;
        }

        $entity = new OrderInfo(
            $s->getName(),
            $s->getPhoneNumber(),
            $s->getEmail(),
            $this->createShippingAddressFromTelegramResponse(
                $s->getShippingAddress()
            ),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    private function createShippingAddressFromTelegramResponse(
        ?TG\Payments\ShippingAddress $s
    ): ?ShippingAddress {
        if (null === $s) {
            return null;
        }

        $entity = new ShippingAddress(
            $s->getCountryCode(),
            $s->getState(),
            $s->getCity(),
            $s->getStreetLine1(),
            $s->getStreetLine2(),
            $s->getPostCode(),
        );
        $this->updateRepository->add($entity);

        return $entity;
    }

    public function createUpdateIfNotExist(
        int $id,
        string $source,
        string $botId,
        ?UpdateChat $chat,
        ?UpdateMessage $message,
        ?CallbackQuery $callbackQuery,
    ): ?Update {
        $isExist = $this->updateRepository->isExistUpdateBySourceUpdateId(
            updateId: $id,
            sourceServiceName: $source,
            botId: $botId
        );
        if (!$isExist) {
            $update = new Update(
                sourceUpdateId: $id,
                sourceServiceName: $source,
                botId: $botId
            );

            $update->setChat($chat);
            $update->setMessage($message);
            $update->setCallbackQuery($callbackQuery);

            $this->updateRepository->add($update, true);

            return $update;
        }

        return null;
    }

    private function createOrGetAndUpdateCallbackQueryFromTelegramResponse(
        TG\CallbackQuery $q
    ): ?CallbackQuery {
        $isExist = $this->updateRepository->isExistCallbackQueryByTelegramId(
            $q->getId()
        );
        if (!$isExist) {
            $callbackQuery = new CallbackQuery(
                telegramId: $q->getId(),
                inlineMessageId: $q->getInlineMessageId(),
                chatInstance: $q->getChatInstance(),
                data: $q->getData(),
                gameShortName: $q->getGameShortName(),
            );

            $this->updateRepository->add($callbackQuery, true);

            return $callbackQuery;
        }

        return null;
    }

}
