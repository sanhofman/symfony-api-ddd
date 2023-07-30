<?php

declare(strict_types=1);

namespace App\Company\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final class CompanyRanking
{
    #[ORM\Column(name: 'ranking')]
    public readonly int $value;

    public function __construct(int $value)
    {
        Assert::positiveInteger($value);

        $this->value = $value;
    }
}
