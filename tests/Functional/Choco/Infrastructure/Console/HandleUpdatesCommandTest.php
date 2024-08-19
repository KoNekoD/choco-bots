<?php

declare(strict_types=1);

namespace App\Tests\Functional\Choco\Infrastructure\Console;

use App\Choco\Domain\Repository\ChocoRepositoryInterface;
use App\Main\Infrastructure\Console\CollectUpdatesCommand;
use App\Shared\Domain\Service\UlidService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

final class HandleUpdatesCommandTest
    extends WebTestCase
{
    private ChocoRepositoryInterface $updateRepository;
    //    private KernelBrowser $client;

    /** @see CollectUpdatesCommand::execute() */
    public function testUpdateTook(): void
    {
        //        $client->getContainer()->get(HttpClientInterface::class)->setResponseFactory([
        //            new MockResponse()
        //        ]);
        $this->assertTrue(true);

        //        $this->runCommand($this->client->getKernel(), 'app:choco:updates:handle');
    }

    protected function runCommand(KernelInterface $kernel, string $name): string
    {
        $app = new Application($kernel);
        $command = $app->find($name);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        return $commandTester->getDisplay();
    }

    /** @throws Exception */
    public function testOneUpdateTook(): void
    {
        $update = $this->updateRepository->findById(
            '01HJVSDWCEBRP925NGE4HY2EPZ'
        );
        if (null === $update) {
            throw new Exception('Update not found');
        }

        $update->tryHandleUpdate();
        $this->assertTrue(true);
        $this->updateRepository->save();
    }

    protected function setUp(): void
    {
        //        $this->client = static::createClient();
        UlidService::$forcedUlidStack = [];
        //        Carbon::setTestNow('2023-01-01');

        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use static::getContainer() to access the service container
        $container = static::getContainer();

        //        $this->withFixtures(static::$kernel);
        /** @var ChocoRepositoryInterface $repo */
        $repo = $this->getContainer()->get(ChocoRepositoryInterface::class);
        $this->updateRepository = $repo;
    }
}
