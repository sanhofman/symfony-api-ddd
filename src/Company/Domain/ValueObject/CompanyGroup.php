<?php

declare(strict_types=1);

namespace App\Company\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final class CompanyGroup
{
    #[ORM\Column(name: 'companyGroup', length: 255)]
    public readonly string $value;

    public function __construct(string $value)
    {
        Assert::lengthBetween($value, 1, 255);

        $this->value = $value;
    }

    public function isEqualTo(self $companyGroup): bool
    {
        return $companyGroup->value === $this->value;
    }
}
