<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use JsonSerializable;
use Stringable;

abstract class BaseId
    implements Stringable, JsonSerializable
{
    public function unserialize(string $data): self
    {
        return static::fromString($data);
    }

    abstract public static function fromString(string $value): self;

    public function isEqual(self $date): bool
    {
        return (string)$date === (string)$this;
    }
}
