<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_callback_query')]
class CallbackQuery
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', length: 26),
        ORM\GeneratedValue(strategy: 'NONE')
    ]
    private string $id;

    public function __construct(
        #[ORM\Column(type: 'string')]
        private readonly string $telegramId,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $inlineMessageId,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $chatInstance,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $data,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $gameShortName,
    ) {
        $this->id = UlidService::generate();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTelegramId(): string
    {
        return $this->telegramId;
    }

    public function getInlineMessageId(): ?string
    {
        return $this->inlineMessageId;
    }

    public function getChatInstance(): ?string
    {
        return $this->chatInstance;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function getGameShortName(): ?string
    {
        return $this->gameShortName;
    }
}
