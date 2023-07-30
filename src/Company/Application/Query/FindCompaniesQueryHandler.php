<?php

declare(strict_types=1);

namespace App\Company\Application\Query;

use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;

final readonly class FindCompaniesQueryHandler implements QueryHandlerInterface
{
    public function __construct(private CompanyRepositoryInterface $repository)
    {
    }

    public function __invoke(FindCompaniesQuery $query): CompanyRepositoryInterface
    {
        $companyRepository = $this->repository;

        if (null !== $query->companyGroup) {
            $companyRepository = $companyRepository->withCompanyGroup($query->companyGroup);
        }

        if (null !== $query->page && null !== $query->itemsPerPage) {
            $companyRepository = $companyRepository->withPagination($query->page, $query->itemsPerPage);
        }

        return $companyRepository;
    }
}
