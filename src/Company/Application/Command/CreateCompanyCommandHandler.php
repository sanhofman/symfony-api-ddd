<?php

declare(strict_types=1);

namespace App\Company\Application\Command;

use App\Company\Domain\Model\Company;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;

final readonly class CreateCompanyCommandHandler implements CommandHandlerInterface
{
    public function __construct(private readonly CompanyRepositoryInterface $companyRepository)
    {
    }

    public function __invoke(CreateCompanyCommand $command): Company
    {
        $company = new Company(
            $command->name,
            $command->group,
            $command->ranking,
        );

        $this->companyRepository->save($company);

        return $company;
    }
}
