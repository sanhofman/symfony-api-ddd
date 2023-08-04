<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Company\Application\Command\UpdateCompanyCommand;
use App\Company\Domain\Model\Company;
use App\Company\Domain\ValueObject\CompanyGroup;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\CompanyName;
use App\Company\Domain\ValueObject\CompanyRanking;
use App\Company\Infrastructure\ApiPlatform\Payload\CompanyWriteModel;
use App\Company\Infrastructure\ApiPlatform\Resource\CompanyResource;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Component\Uid\Ulid;
use Webmozart\Assert\Assert;

final readonly class UpdateCompanyProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): CompanyResource
    {
        Assert::isInstanceOf($data, CompanyWriteModel::class);
        Assert::isInstanceOf($context['previous_data'], CompanyResource::class);

        /** @var CompanyWriteModel $data */
        $command = new UpdateCompanyCommand(
            new CompanyId(Ulid::fromString($context['previous_data']->id)),
            new CompanyName($data->name),
            new CompanyGroup($data->group),
            new CompanyRanking($data->ranking),
        );

        /** @var Company $model */
        $model = $this->commandBus->dispatch($command);

        return CompanyResource::fromModel($model);
    }
}
