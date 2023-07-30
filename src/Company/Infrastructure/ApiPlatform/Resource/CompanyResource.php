<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Company\Domain\Model\Company;
use App\Company\Infrastructure\ApiPlatform\OpenApi\CompanyGroupFilter;
use App\Company\Infrastructure\ApiPlatform\Payload\CompanyWriteModel;
use App\Company\Infrastructure\ApiPlatform\Payload\ResetRankingCompaniesWriteModel;
use App\Company\Infrastructure\ApiPlatform\State\Processor\CreateCompanyProcessor;
use App\Company\Infrastructure\ApiPlatform\State\Processor\DeleteCompanyProcessor;
use App\Company\Infrastructure\ApiPlatform\State\Processor\ResetRankingCompaniesProcessor;
use App\Company\Infrastructure\ApiPlatform\State\Processor\UpdateCompanyProcessor;
use App\Company\Infrastructure\ApiPlatform\State\Provider\CompanyCollectionProvider;
use App\Company\Infrastructure\ApiPlatform\State\Provider\CompanyItemProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'Company',
    operations: [
        // commands
        new Post(
            '/companies/reset-ranking.{_format}',
            status: 202,
            openapiContext: ['summary' => 'Reset ranking of every Company resources'],
            input: ResetRankingCompaniesWriteModel::class,
            output: false,
            processor: ResetRankingCompaniesProcessor::class,
        ),
        // CRUD
        new GetCollection(
            filters: [CompanyGroupFilter::class],
            provider: CompanyCollectionProvider::class,
        ),
        new Get(
            provider: CompanyItemProvider::class,
        ),
        new Post(
            input: CompanyWriteModel::class,
            processor: CreateCompanyProcessor::class,
        ),
        new Put(
            input: CompanyWriteModel::class,
            provider: CompanyItemProvider::class,
            processor: UpdateCompanyProcessor::class,
            extraProperties: ['standard_put' => true],
        ),
        new Patch(
            input: CompanyWriteModel::class,
            provider: CompanyItemProvider::class,
            processor: UpdateCompanyProcessor::class,
        ),
        new Delete(
            provider: CompanyItemProvider::class,
            processor: DeleteCompanyProcessor::class,
        ),
    ],
)]
final class CompanyResource
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $id;
    #[Assert\NotBlank]
    public string $name;

    #[Assert\NotBlank]
    public string $group;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $ranking;

    public function __construct(
        string $id,
        string $name,
        string $group,
        int $ranking,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->group = $group;
        $this->ranking = $ranking;
    }

    public static function fromModel(Company $company): self
    {
        return new self(
            $company->id()->value->jsonSerialize(),
            $company->name()->value,
            $company->group()->value,
            $company->ranking()->value,
        );
    }
}
