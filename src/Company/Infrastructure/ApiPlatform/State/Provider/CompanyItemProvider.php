<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Company\Application\Query\FindCompanyQuery;
use App\Company\Domain\Model\Company;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Infrastructure\ApiPlatform\Resource\CompanyResource;
use App\Shared\Application\Query\QueryBusInterface;
use Symfony\Component\Uid\Ulid;

/**
 * @implements ProviderInterface<CompanyResource>
 */
final readonly class CompanyItemProvider implements ProviderInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?CompanyResource
    {
        /** @var string $id */
        $id = $uriVariables['id'];

        /** @var Company|null $model */
        $model = $this->queryBus->ask(new FindCompanyQuery(new CompanyId(Ulid::fromString($id))));

        return null !== $model ? CompanyResource::fromModel($model) : null;
    }
}
