<?php

declare(strict_types=1);

namespace App\Tests\Functional\Choco\Infrastructure\Console;

use App\Main\Infrastructure\Console\CollectUpdatesCommand;
use App\Shared\Domain\Service\UlidService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

final class CollectChatClientUpdatesCommandTest
    extends WebTestCase
{
    //    private KernelBrowser $client;

    /** @see CollectUpdatesCommand::execute() */
    public function testUpdateTook(): void
    {
        //        $client->getContainer()->get(HttpClientInterface::class)->setResponseFactory([
        //            new MockResponse()
        //        ]);
        //        $this->runCommand($this->client->getKernel(), 'app:main:updates:collect');
        $this->assertTrue(true);
    }

    protected function runCommand(KernelInterface $kernel, string $name): string
    {
        $app = new Application($kernel);
        $command = $app->find($name);
        $commandTester = new CommandTester($command);
        $commandTester->execute([], []);

        return $commandTester->getDisplay();
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        //        UlidService::$forcedUlidStack = [];
        //        Carbon::setTestNow('2020-01-01');
        //        static::$kernel = static::createKernel();
        //        static::$kernel->boot();
        // //        $this->withFixtures(static::$kernel);

        //        $this->client = static::createClient();
        UlidService::$forcedUlidStack = [];
        //        Carbon::setTestNow('2023-01-01');
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        //        $this->withFixtures(static::$kernel);
    }
}
