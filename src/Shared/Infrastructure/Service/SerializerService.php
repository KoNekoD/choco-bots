<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Service\SerializerServiceInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class SerializerService
    implements SerializerServiceInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private NormalizerInterface $normalizer,
        private DenormalizerInterface $denormalizer
    ) {}

    public function serialize(mixed $data, string $format = 'json'): string
    {
        return $this->serializer->serialize(
            $data,
            $format,
            [
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    '__initializer__',
                    '__cloner__',
                    '__isInitialized__',
                ],
            ]
        );
    }

    public function deserialize(
        mixed $data,
        string $type,
        string $format = 'json'
    ): mixed {
        return $this->serializer->deserialize($data, $type, $format);
    }

    /**
     * @throws ExceptionInterface
     */
    public function normalize(mixed $data): mixed
    {
        /* @phpstan-ignore-next-line */
        return $this->normalizer->normalize(
            $data,
            null,
            [
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    '__initializer__',
                    '__cloner__',
                    '__isInitialized__',
                ],
            ]
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function denormalize(
        mixed $data,
        string $type,
        ?string $format = null
    ): mixed {
        return $this->denormalizer->denormalize($data, $type, $format);
    }
}
