<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

trait AggregateRootId
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'string', length: 26, unique: true)]
    public readonly string $value;

    final public function __construct(?Ulid $value = null)
    {
        $this->value = $value ? $value->toBase32() : Ulid::generate();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
