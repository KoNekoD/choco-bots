<?php

declare(strict_types=1);

namespace App\Choco\Infrastructure\Service;

use App\Choco\Domain\ChatClientAPI\ChocoChatClientInterface;
use App\Choco\Domain\Service\ChocoChatClientApiProviderFactoryInterface;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;
use Traversable;

final class ChatClientApiProviderFactory
    implements ChocoChatClientApiProviderFactoryInterface
{
    /**
     * @var ChocoChatClientInterface[]
     */
    private readonly array $apis;

    /** @param iterable<ChocoChatClientInterface> $apis */
    public function __construct(iterable $apis)
    {
        $this->apis = $apis instanceof Traversable
            ? iterator_to_array($apis)
            : $apis;
    }

    public function getApiByServiceName(string $name): ChocoChatClientInterface
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

    /** @return ChocoChatClientInterface[] */
    public function getApiClientList(): array
    {
        return array_values($this->apis);
    }
}
