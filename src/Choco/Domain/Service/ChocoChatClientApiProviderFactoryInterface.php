<?php

declare(strict_types=1);

namespace App\Choco\Domain\Service;

use App\Choco\Domain\ChatClientAPI\ChocoChatClientInterface;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;

interface ChocoChatClientApiProviderFactoryInterface
{
    /** @throws ChatClientAPIException */
    public function getApiByServiceName(string $name): ChocoChatClientInterface;
}
