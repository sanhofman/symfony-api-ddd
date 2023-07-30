<?php

declare(strict_types=1);

namespace App\Tests\Company\Functional;

use App\Company\Application\Command\ResetRankingCompanyCommand;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\ValueObject\CompanyRanking;
use App\Shared\Application\Command\CommandBusInterface;
use App\Tests\Company\DummyFactory\DummyCompanyFactory;
use App\Tests\Shared\Unit\Functional\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ResetRankingCompaniesTest extends KernelTestCase
{
    use ReloadDatabaseTrait;

    public function testResetRankingCompanies(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        /** @var CommandBusInterface $commandBus */
        $commandBus = static::getContainer()->get(CommandBusInterface::class);

        for ($i = 1; $i < 10; ++$i) {
            $companyRepository->save(DummyCompanyFactory::createCompany(ranking: $i));
        }

        $commandBus->dispatch(new ResetRankingCompanyCommand(new CompanyRanking(50)));

        foreach ($companyRepository as $company) {
            self::assertEquals(new CompanyRanking(50), $company->ranking());
        }
    }
}
