<?php

declare(strict_types=1);

namespace App\Main\Infrastructure\Console;

use App\Main\Infrastructure\Service\UpdateProviderService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:main:updates:collect')]
final class CollectUpdatesCommand
    extends Command
{
    public function __construct(
        private readonly UpdateProviderService $updateProvider
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        $this->updateProvider->pullUpdates($io);

        return Command::SUCCESS;
    }
}
