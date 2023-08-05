<?php

declare(strict_types=1);

namespace App\Tests\Company\Acceptance;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Company\Domain\Model\Company;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\ValueObject\CompanyGroup;
use App\Company\Domain\ValueObject\CompanyName;
use App\Company\Domain\ValueObject\CompanyRanking;
use App\Company\Infrastructure\ApiPlatform\Resource\CompanyResource;
use App\Tests\Company\DummyFactory\DummyCompanyFactory;
use App\Tests\Shared\Unit\Functional\ReloadDatabaseTrait;

final class CompanyCrudTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    public function testReturnPaginatedCompanies(): void
    {
        $client = static::createClient();

        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        for ($i = 1; $i <= 100; ++$i) {
            $companyRepository->save(DummyCompanyFactory::createCompany(
                name: sprintf('name_%d', $i),
                group: sprintf('group_%d', $i),
                ranking: $i,
            ));
        }

        $client->request('GET', '/api/companies');

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceCollectionJsonSchema(CompanyResource::class);

        static::assertJsonContains([
            'hydra:totalItems' => 100,
            'hydra:view' => [
                'hydra:first' => '/api/companies?page=1',
                'hydra:last' => '/api/companies?page=5',
                'hydra:next' => '/api/companies?page=2',
            ],
        ]);
    }

    public function testFilterCompaniesByGroup(): void
    {
        $client = static::createClient();

        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        $companyRepository->save(DummyCompanyFactory::createCompany(group: 'groupOne'));
        $companyRepository->save(DummyCompanyFactory::createCompany(group: 'groupTwo'));
        $companyRepository->save(DummyCompanyFactory::createCompany(group: 'groupTwo'));

        $client->request('GET', '/api/companies?companyGroup=groupTwo');

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceCollectionJsonSchema(CompanyResource::class);
        static::assertJsonContains([
            'hydra:totalItems' => 2,
        ]);
    }

    public function testReturnCompany(): void
    {
        $client = static::createClient();

        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        $company = DummyCompanyFactory::createCompany();
        $companyRepository->save($company);

        $client->request('GET', sprintf('/api/companies/%s', $company->id()));

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceItemJsonSchema(CompanyResource::class);

        static::assertJsonContains([
            'id' => $company->id()->value->__toString(),
            'name' => 'name',
            'group' => 'group',
            'ranking' => 1,
        ]);
    }

    public function testCreateCompany(): void
    {
        $client = static::createClient();

        $companyPayload = DummyCompanyFactory::createCompanyWriteModel(name: 'newCompany');

        $response = $client->request('POST', '/api/companies', [
            'json' => $companyPayload->jsonSerialize(),
        ]);

        // Async processing.
        static::assertResponseIsSuccessful();
        static::assertResponseStatusCodeSame(202);
        static::assertEmpty($response->getContent());

        /** @var Company $company */
        $company = static::getContainer()->get(CompanyRepositoryInterface::class)->getMostRecent();

        // Test process finished successfully.
        static::assertNotNull($company);
        static::assertEquals(new CompanyName('newCompany'), $company->name());
        static::assertEquals(new CompanyGroup('group'), $company->group());
        static::assertEquals(new CompanyRanking(1), $company->ranking());
    }

    public function testCannotCreateCompanyWithoutValidPayload(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/companies', [
            'json' => [],
        ]);

        static::assertResponseIsUnprocessable();
        static::assertJsonContains([
            'violations' => [
                ['propertyPath' => 'name', 'message' => 'This value should not be blank.'],
                ['propertyPath' => 'name', 'message' => 'This value is too short. It should have 1 character or more.'],
                ['propertyPath' => 'group', 'message' => 'This value should not be blank.'],
                ['propertyPath' => 'group', 'message' => 'This value is too short. It should have 1 character or more.'],
            ],
        ]);

        $client->request('POST', '/api/companies', [
            'json' => [
                'name' => 'testName',
                'group' => 'testGroup',
                'ranking' => -5,
            ],
        ]);

        static::assertResponseIsUnprocessable();
        static::assertJsonContains([
            'violations' => [
                ['propertyPath' => 'ranking', 'message' => 'This value should be either positive or zero.'],
            ],
        ]);
    }

    public function testUpdateCompany(): void
    {
        $client = static::createClient();

        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        $company = DummyCompanyFactory::createCompany();
        $companyRepository->save($company);

        $response = $client->request('PUT', sprintf('/api/companies/%s', $company->id()), [
            'json' => [
                'name' => 'newName',
                'group' => 'newGroup',
                'ranking' => 20,
            ],
        ]);

        // Async processing.
        static::assertResponseIsSuccessful();
        static::assertResponseStatusCodeSame(202);
        static::assertEmpty($response->getContent());

        // Test process finished successfully.
        $updatedCompany = $companyRepository->ofId($company->id());

        static::assertNotNull($company);
        static::assertEquals(new CompanyName('newName'), $updatedCompany->name());
        static::assertEquals(new CompanyGroup('newGroup'), $updatedCompany->group());
        static::assertEquals(new CompanyRanking(20), $updatedCompany->ranking());
    }

    public function testDeleteCompany(): void
    {
        $client = static::createClient();

        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        $company = DummyCompanyFactory::createCompany();
        $companyRepository->save($company);

        $response = $client->request('DELETE', sprintf('/api/companies/%s', $company->id()));

        // Async processing.
        static::assertResponseIsSuccessful();
        static::assertResponseStatusCodeSame(202);
        static::assertEmpty($response->getContent());

        // Test process finished successfully.
        static::assertNull($companyRepository->ofId($company->id()));
    }
}
