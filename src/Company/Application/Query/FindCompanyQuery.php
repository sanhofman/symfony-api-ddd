<?php

declare(strict_types=1);

namespace App\Company\Application\Query;

use App\Company\Domain\ValueObject\CompanyId;
use App\Shared\Application\Query\QueryInterface;

final readonly class FindCompanyQuery implements QueryInterface
{
    public function __construct(
        public CompanyId $id,
    ) {
    }
}
