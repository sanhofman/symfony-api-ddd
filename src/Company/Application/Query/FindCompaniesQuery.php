<?php

declare(strict_types=1);

namespace App\Company\Application\Query;

use App\Company\Domain\ValueObject\CompanyGroup;
use App\Shared\Application\Query\QueryInterface;

final readonly class FindCompaniesQuery implements QueryInterface
{
    public function __construct(
        public ?CompanyGroup $companyGroup = null,
        public ?int $page = null,
        public ?int $itemsPerPage = null,
    ) {
    }
}
