<?php

declare(strict_types=1);

namespace App\Company\Application\Query;

use App\Company\Domain\Model\Company;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;

final readonly class FindCompanyQueryHandler implements QueryHandlerInterface
{
    public function __construct(private CompanyRepositoryInterface $repository)
    {
    }

    public function __invoke(FindCompanyQuery $query): ?Company
    {
        return $this->repository->ofId($query->id);
    }
}
