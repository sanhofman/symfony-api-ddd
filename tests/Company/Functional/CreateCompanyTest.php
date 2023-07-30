<?php

declare(strict_types=1);

namespace App\Tests\Company\Functional;

use App\Company\Application\Command\CreateCompanyCommand;
use App\Company\Application\Command\UpdateCompanyCommand;
use App\Company\Domain\Model\Company;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\ValueObject\CompanyGroup;
use App\Company\Domain\ValueObject\CompanyName;
use App\Company\Domain\ValueObject\CompanyRanking;
use App\Shared\Application\Command\CommandBusInterface;
use App\Tests\Shared\Unit\Functional\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class CreateCompanyTest extends KernelTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateCompany(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        /** @var CommandBusInterface $commandBus */
        $commandBus = static::getContainer()->get(CommandBusInterface::class);

        static::assertCount(0, $companyRepository);

        $commandBus->dispatch(new CreateCompanyCommand(
            new CompanyName('name'),
            new CompanyGroup('group'),
            new CompanyRanking(1),
        ));

        static::assertCount(1, $companyRepository);

        /** @var Company $company */
        $company = array_values(iterator_to_array($companyRepository))[0];

        static::assertEquals(new CompanyName('name'), $company->name());
        static::assertEquals(new CompanyGroup('group'), $company->group());
        static::assertEquals(new CompanyRanking(1), $company->ranking());
    }

    public function testCompanyTimestampable(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        /** @var CommandBusInterface $commandBus */
        $commandBus = static::getContainer()->get(CommandBusInterface::class);

        $commandBus->dispatch(new CreateCompanyCommand(
            new CompanyName('name'),
            new CompanyGroup('group'),
            new CompanyRanking(1),
        ));

        /** @var Company $company */
        $company = array_values(iterator_to_array($companyRepository))[0];

        static::assertNotNull($company->getCreatedAt());
        static::assertNotNull($company->getChangedAt());

        // Update company to test changedAt.
        $initialChangedAt = $company->getChangedAt();

        sleep(1);
        $commandBus->dispatch(new UpdateCompanyCommand(
            $company->id(),
            new CompanyName('updatedName'),
            $company->group(),
            $company->ranking(),
        ));

        static::assertNotSame($company->getChangedAt(), $initialChangedAt);
    }
}
