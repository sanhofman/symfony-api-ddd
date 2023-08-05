<?php

declare(strict_types=1);

namespace App\Tests\Company\Functional;

use App\Company\Application\Query\FindCompaniesQuery;
use App\Company\Domain\Model\Company;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\ValueObject\CompanyGroup;
use App\Shared\Application\Query\QueryBusInterface;
use App\Tests\Company\DummyFactory\DummyCompanyFactory;
use App\Tests\Shared\Unit\Functional\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class FindCompaniesTest extends KernelTestCase
{
    use ReloadDatabaseTrait;

    public function testFindCompany(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        /** @var QueryBusInterface $queryBus */
        $queryBus = static::getContainer()->get(QueryBusInterface::class);

        $initialCompanies = [
            DummyCompanyFactory::createCompany(),
            DummyCompanyFactory::createCompany(),
            DummyCompanyFactory::createCompany(),
            DummyCompanyFactory::createCompany(),
            DummyCompanyFactory::createCompany(),
        ];

        foreach ($initialCompanies as $company) {
            $companyRepository->save($company);
        }

        $companies = $queryBus->ask(new FindCompaniesQuery());

        static::assertCount(count($initialCompanies), $companies);
        foreach ($companies as $company) {
            static::assertContains($company, $initialCompanies);
        }
    }

    public function testFilterCompaniesByGroup(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        /** @var QueryBusInterface $queryBus */
        $queryBus = static::getContainer()->get(QueryBusInterface::class);

        $companyRepository->save(DummyCompanyFactory::createCompany(group: 'groupOne'));
        $companyRepository->save(DummyCompanyFactory::createCompany(group: 'groupTwo'));
        $companyRepository->save(DummyCompanyFactory::createCompany(group: 'groupTwo'));

        static::assertCount(3, $companyRepository);

        /** @var Company[] $companies */
        $companies = $queryBus->ask(new FindCompaniesQuery(companyGroup: new CompanyGroup('groupTwo')));

        static::assertCount(2, $companies);
        foreach ($companies as $company) {
            static::assertEquals(new CompanyGroup('groupTwo'), $company->group());
        }
    }

    public function testReturnPaginatedCompanies(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        /** @var QueryBusInterface $queryBus */
        $queryBus = static::getContainer()->get(QueryBusInterface::class);

        $initialCompanies = [
            DummyCompanyFactory::createCompany(),
            DummyCompanyFactory::createCompany(),
            DummyCompanyFactory::createCompany(),
            DummyCompanyFactory::createCompany(),
            DummyCompanyFactory::createCompany(),
        ];

        foreach ($initialCompanies as $company) {
            $companyRepository->save($company);
        }

        static::assertCount(count($initialCompanies), $companyRepository);

        $companies = $queryBus->ask(new FindCompaniesQuery(page: 2, itemsPerPage: 2));

        static::assertCount(2, $companies);

        $i = 0;
        foreach ($companies as $company) {
            static::assertSame($initialCompanies[$i + 2], $company);
            ++$i;
        }
    }
}
