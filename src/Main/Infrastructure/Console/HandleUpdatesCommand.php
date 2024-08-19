<?php

declare(strict_types=1);

namespace App\Main\Infrastructure\Console;

use App\Main\Infrastructure\Service\UpdateProviderService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:main:updates:handle')]
final class HandleUpdatesCommand
    extends Command
{
    use LockableTrait;

    public function __construct(
        private readonly UpdateProviderService $updateProvider
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $this->updateProvider->handleUpdates();

        return Command::SUCCESS;
    }
}
