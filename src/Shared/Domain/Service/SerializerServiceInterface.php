<?php

declare(strict_types=1);

namespace App\Shared\Domain\Service;

interface SerializerServiceInterface
{
    /**
     * @template T
     *
     * @param T $data
     */
    public function serialize(mixed $data, string $format = 'json'): string;

    /**
     * @template T
     *
     * @param class-string<T> $type
     *
     * @return T
     */
    public function deserialize(
        mixed $data,
        string $type,
        string $format = 'json'
    ): mixed;

    /**
     * @template T
     *
     * @param T $data
     *
     * @return array<T>
     */
    public function normalize(mixed $data): mixed;

    /**
     * @template T
     * @template T2
     *
     * @param T $data
     * @param class-string<T2> $type
     *
     * @return T2
     */
    public function denormalize(
        mixed $data,
        string $type,
        ?string $format = null
    ): mixed;
}
