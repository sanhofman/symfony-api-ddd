<?php

declare(strict_types=1);

namespace App\Company\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AggregateRootId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class CompanyId implements \Stringable
{
    use AggregateRootId;
}
