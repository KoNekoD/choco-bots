<?php

declare(strict_types=1);

namespace App\Main\Infrastructure\Service;

use App\Main\Domain\ChatClientAPI\ChatClientInterface;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;
use App\Main\Domain\Service\ChatClientApiProviderFactoryInterface;
use Traversable;

final class ChatClientApiProviderFactory
    implements ChatClientApiProviderFactoryInterface
{
    /**
     * @var ChatClientInterface[]
     */
    private readonly array $apis;

    /** @param iterable<ChatClientInterface> $apis */
    public function __construct(iterable $apis)
    {
        $this->apis = $apis instanceof Traversable
            ? iterator_to_array($apis) :
            $apis;
    }

    public function getApiByServiceName(string $name): ChatClientInterface
    {
        foreach ($this->apis as $api) {
            if ($api::getClientAdapterName() === $name) {
                return $api;
            }
        }

        throw new ChatClientAPIException("Unknown service name. got: $name");
    }

    /** @return array<int, string> */
    public function getList(): array
    {
        return array_keys($this->apis);
    }

    /** @return ChatClientInterface[] */
    public function getApiClientList(): array
    {
        return array_values($this->apis);
    }
}
