<?php

declare(strict_types=1);

namespace App\Tests\Company\Acceptance;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Company\Domain\Model\Company;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\ValueObject\CompanyGroup;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\CompanyName;
use App\Company\Domain\ValueObject\CompanyRanking;
use App\Company\Infrastructure\ApiPlatform\Resource\CompanyResource;
use App\Tests\Company\DummyFactory\DummyCompanyFactory;
use App\Tests\Shared\Unit\Functional\ReloadDatabaseTrait;
use Symfony\Component\Uid\Ulid;

final class CompanyCrudTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    public function testReturnPaginatedCompanies(): void
    {
        $client = static::createClient();

        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        for ($i = 1; $i < 100; ++$i) {
            $companyRepository->save(DummyCompanyFactory::createCompany(
                name: sprintf('name_%d', $i),
                group: sprintf('group_%d', $i),
                ranking: $i,
            ));
        }

        $client->request('GET', '/api/companies');

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceCollectionJsonSchema(CompanyResource::class);
    }

    public function testFilterCompaniesByGroup(): void
    {
        $client = static::createClient();

        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        $companyRepository->save(DummyCompanyFactory::createCompany(group: 'groupOne'));
        $companyRepository->save(DummyCompanyFactory::createCompany(group: 'groupTwo'));
        $companyRepository->save(DummyCompanyFactory::createCompany(group: 'groupTwo'));

        $client->request('GET', '/api/companies?group=groupTwo');

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceCollectionJsonSchema(CompanyResource::class);
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
            'id' => $company->id()->value,
            'name' => 'name',
            'group' => 'group',
            'ranking' => 1,
        ]);
    }

    public function testCreateCompany(): void
    {
        $client = static::createClient();

        $companyPayload = DummyCompanyFactory::createCompanyWriteModel();

        $response = $client->request('POST', '/api/companies', [
            'json' => $companyPayload->jsonSerialize(),
        ]);

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceItemJsonSchema(CompanyResource::class);

        static::assertJsonContains([
            'id' => $response->toArray()['id'],
            'name' => 'name',
            'group' => 'group',
            'ranking' => 1,
        ]);

        $id = new CompanyId(Ulid::fromString(str_replace('/api/companies/', '', $response->toArray()['id'])));

        /** @var Company $company */
        $company = static::getContainer()->get(CompanyRepositoryInterface::class)->ofId($id);

        static::assertNotNull($company);
        static::assertEquals($id, $company->id());
        static::assertEquals(new CompanyName('name'), $company->name());
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

        $client->request('PUT', sprintf('/api/companies/%s', $company->id()), [
            'json' => [
                'name' => 'newName',
                'group' => 'newGroup',
                'ranking' => 20,
            ],
        ]);

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceItemJsonSchema(CompanyResource::class);

        static::assertJsonContains([
            'name' => 'newName',
            'group' => 'newGroup',
            'ranking' => 20,
        ]);

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

        static::assertResponseIsSuccessful();
        static::assertEmpty($response->getContent());

        static::assertNull($companyRepository->ofId($company->id()));
    }
}
