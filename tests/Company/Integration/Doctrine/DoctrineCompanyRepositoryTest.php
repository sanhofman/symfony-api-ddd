<?php

declare(strict_types=1);

namespace App\Tests\Company\Integration\Doctrine;

use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\ValueObject\CompanyGroup;
use App\Shared\Infrastructure\Doctrine\DoctrinePaginator;
use App\Tests\Company\DummyFactory\DummyCompanyFactory;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

final class DoctrineCompanyRepositoryTest extends KernelTestCase
{
    private static Connection $connection;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::$connection = static::getContainer()->get(Connection::class);

        (new Application(static::$kernel))
            ->find('doctrine:database:create')
            ->run(new ArrayInput(['--if-not-exists' => true]), new NullOutput());

        (new Application(static::$kernel))
            ->find('doctrine:schema:update')
            ->run(new ArrayInput(['--force' => true]), new NullOutput());
    }

    protected function setUp(): void
    {
        static::$connection->executeStatement('TRUNCATE company');
    }

    public function testSave(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        static::assertEmpty($companyRepository);

        $company = DummyCompanyFactory::createCompany();
        $companyRepository->save($company);

        static::assertCount(1, $companyRepository);
    }

    public function testRemove(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        $company = DummyCompanyFactory::createCompany();
        $companyRepository->save($company);

        static::assertCount(1, $companyRepository);

        $companyRepository->remove($company);
        static::assertEmpty($companyRepository);
    }

    public function testOfId(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        static::assertEmpty($companyRepository);

        $company = DummyCompanyFactory::createCompany();
        $companyRepository->save($company);

        static::getContainer()->get(EntityManagerInterface::class)->clear();

        static::assertEquals($company->name(), $companyRepository->ofId($company->id())->name());
    }

    public function testGetMostRecent(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        static::assertEmpty($companyRepository);

        $companies = [
            DummyCompanyFactory::createCompany(),
            DummyCompanyFactory::createCompany(),
            DummyCompanyFactory::createCompany(name: 'mostRecent'),
        ];

        foreach ($companies as $company) {
            $companyRepository->save($company);
        }

        static::getContainer()->get(EntityManagerInterface::class)->clear();

        static::assertEquals('mostRecent', $companyRepository->getMostRecent()->name()->value);
    }

    public function testWithCompanyGroup(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        $companyRepository->save(DummyCompanyFactory::createCompany(group: 'groupOne'));
        $companyRepository->save(DummyCompanyFactory::createCompany(group: 'groupOne'));
        $companyRepository->save(DummyCompanyFactory::createCompany(group: 'groupTwo'));

        static::assertCount(2, $companyRepository->withCompanyGroup(new CompanyGroup('groupOne')));
        static::assertCount(1, $companyRepository->withCompanyGroup(new CompanyGroup('groupTwo')));
    }

    public function testWithPagination(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        static::assertNull($companyRepository->paginator());

        $repository = $companyRepository->withPagination(1, 2);

        static::assertInstanceOf(DoctrinePaginator::class, $repository->paginator());
    }

    public function testWithoutPagination(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        $repository = $companyRepository->withPagination(1, 2);
        static::assertNotNull($repository->paginator());

        $repository = $repository->withoutPagination();
        static::assertNull($repository->paginator());
    }

    public function testIteratorWithoutPagination(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        static::assertNull($companyRepository->paginator());

        $companies = [
            DummyCompanyFactory::createCompany(),
            DummyCompanyFactory::createCompany(),
            DummyCompanyFactory::createCompany(),
        ];

        foreach ($companies as $company) {
            $companyRepository->save($company);
        }

        $i = 0;
        foreach ($companyRepository as $company) {
            static::assertSame($companies[$i], $company);
            ++$i;
        }
    }

    public function testIteratorWithPagination(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        static::assertNull($companyRepository->paginator());

        $companies = [
            DummyCompanyFactory::createCompany(),
            DummyCompanyFactory::createCompany(),
            DummyCompanyFactory::createCompany(),
        ];

        foreach ($companies as $company) {
            $companyRepository->save($company);
        }

        $companyRepository = $companyRepository->withPagination(1, 2);

        $i = 0;
        foreach ($companyRepository as $company) {
            static::assertContains($company, $companies);
            ++$i;
        }

        static::assertSame(2, $i);

        $companyRepository = $companyRepository->withPagination(2, 2);

        $i = 0;
        foreach ($companyRepository as $company) {
            static::assertContains($company, $companies);
            ++$i;
        }

        static::assertSame(1, $i);
    }

    public function testCount(): void
    {
        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        $companies = [
            DummyCompanyFactory::createCompany(),
            DummyCompanyFactory::createCompany(),
            DummyCompanyFactory::createCompany(),
        ];

        foreach ($companies as $company) {
            $companyRepository->save($company);
        }

        static::assertCount(count($companies), $companyRepository);
        static::assertCount(2, $companyRepository->withPagination(1, 2));
    }
}
