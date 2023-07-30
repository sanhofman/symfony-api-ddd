<?php

declare(strict_types=1);

namespace App\Shared\Domain\Model;

interface TimestampableInterface
{
    public const DEFAULT_FORMAT = 'c';

    /**
     * Return the date the object implementing this interface was created.
     */
    public function getCreatedAt(): \DateTime;

    /**
     * Return the date the object implementing this interface was last updated.
     */
    public function getChangedAt(): \DateTime;
}
