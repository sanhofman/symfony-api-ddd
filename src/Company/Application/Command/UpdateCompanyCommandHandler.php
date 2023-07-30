<?php

declare(strict_types=1);

namespace App\Company\Application\Command;

use App\Company\Domain\Exception\MissingCompanyException;
use App\Company\Domain\Model\Company;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;

final readonly class UpdateCompanyCommandHandler implements CommandHandlerInterface
{
    public function __construct(private CompanyRepositoryInterface $companyRepository)
    {
    }

    public function __invoke(UpdateCompanyCommand $command): Company
    {
        $company = $this->companyRepository->ofId($command->id);
        if (null === $company) {
            throw new MissingCompanyException($command->id);
        }

        $company->update(
            name: $command->name,
            group: $command->group,
            ranking: $command->ranking,
        );

        $this->companyRepository->save($company);

        return $company;
    }
}
