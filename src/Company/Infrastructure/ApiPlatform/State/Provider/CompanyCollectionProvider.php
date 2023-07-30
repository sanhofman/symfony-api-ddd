<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use App\Company\Application\Query\FindCompaniesQuery;
use App\Company\Domain\Model\Company;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\ValueObject\CompanyGroup;
use App\Company\Infrastructure\ApiPlatform\Resource\CompanyResource;
use App\Shared\Application\Query\QueryBusInterface;
use App\Shared\Infrastructure\ApiPlatform\State\Paginator;

/**
 * @implements ProviderInterface<CompanyResource>
 */
final readonly class CompanyCollectionProvider implements ProviderInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private Pagination $pagination,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var string|null $companyGroup */
        $companyGroup = $context['filters']['companyGroup'] ?? null;
        $offset = $limit = null;

        if ($this->pagination->isEnabled($operation, $context)) {
            $offset = $this->pagination->getPage($context);
            $limit = $this->pagination->getLimit($operation, $context);
        }

        /** @var CompanyRepositoryInterface $models */
        $models = $this->queryBus->ask(new FindCompaniesQuery(null !== $companyGroup ? new CompanyGroup($companyGroup) : null, $offset, $limit));

        $resources = [];
        /** @var Company $model */
        foreach ($models as $model) {
            $resources[] = CompanyResource::fromModel($model);
        }

        if (null !== $paginator = $models->paginator()) {
            $resources = new Paginator(
                new \ArrayIterator($resources),
                (float) $paginator->getCurrentPage(),
                (float) $paginator->getItemsPerPage(),
                (float) $paginator->getLastPage(),
                (float) $paginator->getTotalItems(),
            );
        }

        return $resources;
    }
}
