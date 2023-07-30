<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Company\Application\Command\ResetRankingCompanyCommand;
use App\Company\Domain\Model\Company;
use App\Company\Domain\ValueObject\CompanyRanking;
use App\Company\Infrastructure\ApiPlatform\Payload\ResetRankingCompaniesWriteModel;
use App\Shared\Application\Command\CommandBusInterface;
use Webmozart\Assert\Assert;

final readonly class ResetRankingCompaniesProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        Assert::isInstanceOf($data, ResetRankingCompaniesWriteModel::class);

        /** @var ResetRankingCompaniesWriteModel $data */
        $command = new ResetRankingCompanyCommand(
            new CompanyRanking($data->ranking),
        );

        /* @var Company $model */
        $this->commandBus->dispatch($command);
    }
}
