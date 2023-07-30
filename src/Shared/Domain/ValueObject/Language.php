<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class Language
{
    protected string $value;

    public function __construct(string $value)
    {
        Assert::length($value, 2, 'Invalid language code');

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
