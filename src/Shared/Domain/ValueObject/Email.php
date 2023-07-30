<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class Email
{
    protected string $value;

    public function __construct(string $value)
    {
        Assert::email($value);

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
