<?php

declare(strict_types=1);

namespace App\Main\Infrastructure\Controller;

use App\Main\Application\Service\AbstractChatCommandHandlerFactory;
use App\Main\Infrastructure\DTO\ChatCommandViewItemDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/docs')]
final class ChatCommandViewController
    extends AbstractController
{
    public function __construct(
        private readonly AbstractChatCommandHandlerFactory $chatCommandHandlerFactory
    ) {}

    public function __invoke(): Response
    {
        /** @var ChatCommandViewItemDTO[] $commands */
        $commands = [];
        foreach ($this->chatCommandHandlerFactory->getList() as $command) {
            $commands[] = ChatCommandViewItemDTO::fromAbstractCommand(
                $command
            );
        }

        return new JsonResponse($commands);
    }
}
