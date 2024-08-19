<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Fun\RolePlay;

use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Shared\Application\Command\CommandHandlerInterface;

final class ReplyRolePlayCommandHandler
    implements CommandHandlerInterface
{
    public function __invoke(ReplyRolePlayCommand $command): ChatClientResultDTO
    {
        $who = $command->chocoData->who;
        $targetUpdateUser = $command->chocoData->getReplyFrom();

        $rolePlayAction = $command->getRolePlayAction();
        $postfixPosition = strpos($rolePlayAction, 'ть');
        if (false === $postfixPosition) {
            return ChatClientResultDTO::fail(
                "RP Action $rolePlayAction is not translatable",
                true
            );
        }

        $pastTimeAction = str_replace(
            'ть',
            'л',
            $rolePlayAction
        );

        $additionalText = '';
        $additionalTextMessage = $command->getRolePlayAdditionalMessage();
        if ('' !== $additionalTextMessage) {
            $additionalText = " со словами: $additionalTextMessage";
        }

        $command->chocoData->client->deleteMessage(
            $command->data->getUpdateMessage()
        );

        $command->chocoData->client->sendMessage(
            $command->getSourceChatId(),
            $command->chocoData->client->getChatMemberMentionString(
                $who->getUserFirstName(),
                $who->getUser()->getUpdateUser()
            ).' '.$pastTimeAction.' '.
            $command->chocoData->client->getChatMemberMentionString(
                $targetUpdateUser->getFirstName(),
                $targetUpdateUser
            )
            .$additionalText
        );

        return ChatClientResultDTO::success();
    }
}
