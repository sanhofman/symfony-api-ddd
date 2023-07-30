<?php

declare(strict_types=1);

namespace App\Tests\Company\Functional;

use App\Company\Application\Command\UpdateCompanyCommand;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\ValueObject\CompanyGroup;
use App\Company\Domain\ValueObject\CompanyName;
use App\Company\Domain\ValueObject\CompanyRanking;
use App\Shared\Application\Command\CommandBusInterface;
use App\Tests\Company\DummyFactory\DummyCompanyFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class UpdateCompanyTest extends KernelTestCase
{
    public function testUpdateCompany(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        /** @var CommandBusInterface $commandBus */
        $commandBus = static::getContainer()->get(CommandBusInterface::class);

        $initialCompany = DummyCompanyFactory::createCompany();

        $companyRepository->save($initialCompany);

        $commandBus->dispatch(new UpdateCompanyCommand(
            $initialCompany->id(),
            name: new CompanyName('newName'),
            group: new CompanyGroup('newGroup'),
            ranking: new CompanyRanking(50),
        ));

        $company = $companyRepository->ofId($initialCompany->id());

        static::assertEquals(new CompanyName('newName'), $company->name());
        static::assertEquals(new CompanyGroup('newGroup'), $company->group());
        static::assertEquals(new CompanyRanking(50), $company->ranking());
    }
}
