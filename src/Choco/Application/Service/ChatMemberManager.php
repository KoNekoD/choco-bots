<?php

declare(strict_types=1);

namespace App\Choco\Application\Service;

use App\Choco\Domain\Entity\Choco\ChatMember;
use App\Choco\Domain\Repository\ChatMemberRepositoryInterface;
use App\Choco\Domain\Service\ChocoChatClientApiProviderFactoryInterface;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;

final readonly class ChatMemberManager
{
    public function __construct(
        private ChocoChatClientApiProviderFactoryInterface $apiProviderFactory,
        private ChatMemberRepositoryInterface $chatMemberRepo,
    ) {}

    /**
     * @throws ChatClientAPIException
     */
    public function syncChatMemberInformation(
        string $sourceServiceName,
        int $sourceServiceChatId,
        int $sourceServiceUserId,
    ): ChatMember {
        $client = $this->apiProviderFactory->getApiByServiceName(
            $sourceServiceName
        );

        $chatMember = $client->getChatMember(
            $sourceServiceChatId,
            $sourceServiceUserId
        );

        $this->chatMemberRepo->save();

        return $chatMember;
    }
}
