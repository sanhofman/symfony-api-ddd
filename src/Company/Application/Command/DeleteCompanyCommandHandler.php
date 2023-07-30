<?php

declare(strict_types=1);

namespace App\Company\Application\Command;

use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;

final readonly class DeleteCompanyCommandHandler implements CommandHandlerInterface
{
    public function __construct(private CompanyRepositoryInterface $companyRepository)
    {
    }

    public function __invoke(DeleteCompanyCommand $command): void
    {
        if (null === $company = $this->companyRepository->ofId($command->id)) {
            return;
        }

        $this->companyRepository->remove($company);
    }
}
