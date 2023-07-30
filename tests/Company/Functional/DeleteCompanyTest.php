<?php

declare(strict_types=1);

namespace App\Tests\Company\Functional;

use App\Company\Application\Command\DeleteCompanyCommand;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Shared\Application\Command\CommandBusInterface;
use App\Tests\Company\DummyFactory\DummyCompanyFactory;
use App\Tests\Shared\Unit\Functional\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DeleteCompanyTest extends KernelTestCase
{
    use ReloadDatabaseTrait;

    public function testDeleteCompany(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        /** @var CommandBusInterface $commandBus */
        $commandBus = static::getContainer()->get(CommandBusInterface::class);

        $company = DummyCompanyFactory::createCompany();
        $companyRepository->save($company);

        static::assertCount(1, $companyRepository);

        $commandBus->dispatch(new DeleteCompanyCommand($company->id()));

        static::assertEmpty($companyRepository);
    }
}
