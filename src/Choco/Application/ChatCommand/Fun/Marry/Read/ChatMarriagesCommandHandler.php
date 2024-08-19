<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Fun\Marry\Read;

use App\Choco\Domain\Repository\ChocoRepositoryInterface;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Shared\Application\Command\CommandHandlerInterface;
use Carbon\Carbon;

final readonly class ChatMarriagesCommandHandler
    implements CommandHandlerInterface
{
    public function __construct(
        private ChocoRepositoryInterface $chocoRepository,
    ) {}

    public function __invoke(ChatMarriagesCommand $command): ChatClientResultDTO
    {
        $now = Carbon::now()->toDateTimeImmutable();
        $marries = $this->chocoRepository->getMarriesByChat(
            $command->chocoData->chat
        );

        $text = "💍 БРАКИ ЭТОЙ БЕСЕДЫ\n";
        foreach ($marries as $i => $marry) {
            if (!$marry->isMarryGeneralStatusMarried()) {
                continue;
            }
            // 1. Куратор + Кот (11 месяцев 16 дн)

            $intervalString = ' ';

            $spentInterval = $marry->getCreatedAt()->diff($now);
            if ($spentInterval->y !== 0) {
                $intervalString .= "$spentInterval->y лет ";
            }
            if ($spentInterval->m !== 0) {
                $intervalString .= "$spentInterval->m месяцев ";
            }
            //                if ($spentInterval->d) {
            $intervalString .= "$spentInterval->d дней ";
            //                }

            $text .= sprintf(
                "%d. %s (%s)\n",
                $i,
                $marry->getFullParticipantsFirstNamesString(),
                $intervalString
            );
        }

        $command->chocoData->client->sendMessage(
            $command->getSourceChatId(),
            $text
        );

        return ChatClientResultDTO::success();
    }
}
