<?php

declare(strict_types=1);

namespace App\Tests\Company\Acceptance;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Company\Domain\Model\Company;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\ValueObject\CompanyRanking;
use App\Tests\Company\DummyFactory\DummyCompanyFactory;

final class ResetRankingCompaniesTest extends ApiTestCase
{
    public function testResetRankingOfCompanies(): void
    {
        $client = static::createClient();

        /** @var CompanyRepositoryInterface $companyRepository */
        $companyRepository = static::getContainer()->get(CompanyRepositoryInterface::class);

        for ($i = 1; $i < 10; ++$i) {
            $companyRepository->save(DummyCompanyFactory::createCompany(
                name: sprintf('name_%d', $i),
                group: sprintf('group_%d', $i),
                ranking: $i,
            ));
        }

        $response = $client->request('POST', '/api/companies/reset-ranking', [
            'json' => [
                'ranking' => 10,
            ],
        ]);

        static::assertResponseStatusCodeSame(202);
        static::assertEmpty($response->getContent());

        /** @var Company $company */
        foreach ($companyRepository as $company) {
            self::assertEquals(new CompanyRanking(10), $company->ranking());
        }
    }
}
