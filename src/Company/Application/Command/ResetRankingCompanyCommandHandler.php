<?php

declare(strict_types=1);

namespace App\Company\Application\Command;

use App\Company\Domain\Model\Company;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;

final readonly class ResetRankingCompanyCommandHandler implements CommandHandlerInterface
{
    public function __construct(private CompanyRepositoryInterface $companyRepository)
    {
    }

    public function __invoke(ResetRankingCompanyCommand $command): void
    {
        $companies = $this->companyRepository->withoutPagination();

        /** @var Company $company */
        foreach ($companies as $company) {
            $company->update(
                ranking: $command->companyRanking,
            );

            $this->companyRepository->save($company);
        }
    }
}
