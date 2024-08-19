<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Telemetry\ChatMessages\Daily;

use App\Choco\Domain\Repository\ChocoRepositoryInterface;
use App\Main\Domain\DTO\ChatClientResultDTO;
use App\Shared\Application\Command\CommandHandlerInterface;
use Carbon\Carbon;

final readonly class GetStatisticsCommandHandler
    implements CommandHandlerInterface
{
    public function __construct(
        private ChocoRepositoryInterface $chocoRepository
    ) {}

    public function __invoke(GetStatisticsCommand $command): ChatClientResultDTO
    {
        $who = $command->chocoData->who;
        $now = Carbon::now()->toDateTimeImmutable();
        $stats = $this->chocoRepository->getMessagesStatsAggregatedByChatAndTimeRange(
            $now->modify('-1 day'),
            $now,
            $who->getChat()
        );

        $result = '📊 СТАТИСТИКА ПО СООБЩЕНИЯМ ЗА СУТКИ';
        foreach ($stats as $stat) {
            $firstname = $stat->user->getUpdateUser()->getFirstName();
            $result .= "\n $firstname - $stat->messagesCount";
        }

        return $command->chocoData->client->sendMessage(
            $command->getSourceChatId(),
            $result,
        );
    }
}
