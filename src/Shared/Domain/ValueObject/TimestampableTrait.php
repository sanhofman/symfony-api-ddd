<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

/**
 * Trait to enable timestamps on entities for created/changed info, handled by TimestampableSubscriber.
 */
trait TimestampableTrait
{
    private \DateTime $created;

    private \DateTime $changed;

    public function getCreatedAt(): \DateTime
    {
        return $this->created;
    }

    public function getChangedAt(): \DateTime
    {
        return $this->changed;
    }
}
