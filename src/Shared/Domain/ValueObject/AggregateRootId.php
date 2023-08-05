<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Ulid;

trait AggregateRootId
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'ulid', unique: true)]
    public readonly AbstractUid $value;

    final public function __construct(?AbstractUid $value = null)
    {
        $this->value = $value ?? new Ulid();
    }

    public function __toString(): string
    {
        return $this->value->toBase32();
    }
}
