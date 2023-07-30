<?php

declare(strict_types=1);

namespace App\Tests\Company\Functional;

use App\Company\Application\Query\FindCompanyQuery;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Shared\Application\Query\QueryBusInterface;
use App\Tests\Company\DummyFactory\DummyCompanyFactory;
use App\Tests\Shared\Unit\Functional\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class FindCompanyTest extends KernelTestCase
{
    use ReloadDatabaseTrait;

    public function testFindCompany(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        /** @var QueryBusInterface $queryBus */
        $queryBus = static::getContainer()->get(QueryBusInterface::class);

        $company = DummyCompanyFactory::createCompany();
        $companyRepository->save($company);

        static::assertSame($company, $queryBus->ask(new FindCompanyQuery($company->id())));
    }
}
