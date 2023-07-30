<?php

declare(strict_types=1);

namespace App\Company\Domain\Repository;

use App\Company\Domain\Model\Company;
use App\Company\Domain\ValueObject\CompanyGroup;
use App\Company\Domain\ValueObject\CompanyId;
use App\Shared\Domain\Repository\RepositoryInterface;

interface CompanyRepositoryInterface extends RepositoryInterface
{
    public function save(Company $company): void;

    public function remove(Company $company): void;

    public function ofId(CompanyId $id): ?Company;

    public function withCompanyGroup(CompanyGroup $companyGroup): static;
}
