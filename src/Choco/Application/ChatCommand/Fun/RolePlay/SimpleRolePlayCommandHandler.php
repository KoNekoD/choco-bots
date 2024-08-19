<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Fun\RolePlay;

use App\Choco\Domain\Exception\ChocoEntity\ChatMemberException;
use App\Choco\Domain\Repository\ChatMemberRepositoryInterface;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;
use App\Shared\Application\Command\CommandHandlerInterface;

final readonly class SimpleRolePlayCommandHandler
    implements CommandHandlerInterface
{
    public function __construct(
        private ChatMemberRepositoryInterface $chatMemberRepository,
    ) {}

    /**
     * @throws ChatMemberException
     * @throws ChatClientAPIException
     */
    public function __invoke(
        SimpleRolePlayCommand $command
    ): ChatClientResultDTO {
        $who = $command->chocoData->who;
        $target = $this->chatMemberRepository->findChatMemberByFirstMentionOrUsername(
            $command->data->getUpdate(),
            $command->getRolePlayTarget()
        );

        $postfixPosition = strpos($command->getRolePlayAction(), 'ть');
        if (false === $postfixPosition) {
            return ChatClientResultDTO::fail(
                "RP Action {$command->getRolePlayAction()} is not translatable",
                true
            );
        }

        $command->chocoData->client->deleteMessage(
            $command->data->getUpdateMessage()
        );

        $whoMention = $command->chocoData->client->getChatMemberMentionString(
            $who->getUserFirstName(),
            $who->getUser()->getUpdateUser()
        );

        $rolePlayAction = $command->getRolePlayAction();
        $pastTimeAction = str_replace('ть', 'л', $rolePlayAction);

        $targetMention = $command->chocoData->client->getChatMemberMentionString(
            $target->getUser()->getUpdateUser()->getFirstName(),
            $target->getUser()->getUpdateUser()
        );

        $additionalText = '';
        $additionalTextMessage = $command->getRolePlayAdditionalMessage();
        if ('' !== $additionalTextMessage) {
            $additionalText = " со словами: $additionalTextMessage";
        }

        $command->chocoData->client->sendMessage(
            $command->getSourceChatId(),
            $whoMention.' '.$pastTimeAction.' '.$targetMention.$additionalText
        );

        return ChatClientResultDTO::success();
    }
}
