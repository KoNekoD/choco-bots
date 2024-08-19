<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Fun\Marry\Create;

use App\Choco\Domain\Entity\Choco\ChatMember;
use App\Choco\Domain\Exception\Marry\MarryException;
use App\Choco\Domain\Factory\MarryFactory;
use App\Choco\Domain\Repository\ChatMemberRepositoryInterface;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Shared\Application\Command\CommandHandlerInterface;

final readonly class SendMarryRequestCommandHandler
    implements CommandHandlerInterface
{
    public function __construct(
        private ChatMemberRepositoryInterface $chatMemberRepository,
        private MarryFactory $marryFactory,
    ) {}

    public function __invoke(
        SendMarryRequestCommand $command
    ): ChatClientResultDTO {
        $who = $command->chocoData->who;

        $targetList = $this
            ->chatMemberRepository
            ->findChatMembersByMentionOrUsername(
                $command->data->getUpdate(),
                $command->getTargetUsernameList()
            );

        /** @var ChatMember[] $marryList */
        $marryList = [$who, ...$targetList];

        $rawTargets = $command->getTargetUsernameListRaw();

        foreach ($targetList as $targetItem) {
            if ($targetItem->getId() === $who->getId()) {
                $command->chocoData->client->sendMessage(
                    $command->getSourceChatId(),
                    'Сделать предложение самому себе? Так делать нельзя'
                );

                return ChatClientResultDTO::fail(
                    'Сделать предложение самому себе? Так делать нельзя',
                    true
                );
            }
        }

        if (count($marryList) < 2) {
            $command->chocoData->client->sendMessage(
                $command->getSourceChatId(),
                'Количество участников брака должно быть больше одного человека'
            );

            return ChatClientResultDTO::fail(
                'Количество участников брака должно быть больше одного человека',
                true
            );
        }

        if (count($targetList) !== count($rawTargets)) {
            $unknownTargets = $rawTargets;
            foreach ($targetList as $target) {
                foreach ($unknownTargets as $i => $unknownTarget) {
                    if (
                        str_contains(
                            $unknownTarget,
                            $target->getUserUsernameOrFirstName()
                        )
                    ) {
                        unset($unknownTargets[$i]);
                    }
                }
            }
            $command->chocoData->client->sendMessage(
                $command->getSourceChatId(),
                'unknown targets: '.
                implode(', ', $unknownTargets)
            );

            return ChatClientResultDTO::fail(
                'Marry unknown targets: '.
                implode(', ', $unknownTargets),
                true
            );
        }

        try {
            $this->marryFactory->create($who, $marryList);

            return ChatClientResultDTO::success();
        } catch (MarryException $e) {
            $command->chocoData->client->sendMessage(
                $command->getSourceChatId(),
                $e->getMessage()
            );

            return ChatClientResultDTO::fatal($e);
        }
    }
}
