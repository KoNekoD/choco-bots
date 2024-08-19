<?php

declare(strict_types=1);

namespace App\Main\Domain\Service;

use App\Main\Domain\ChatClientAPI\ChatClientInterface;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;

interface ChatClientApiProviderFactoryInterface
{
    /** @throws ChatClientAPIException */
    public function getApiByServiceName(string $name): ChatClientInterface;
}
