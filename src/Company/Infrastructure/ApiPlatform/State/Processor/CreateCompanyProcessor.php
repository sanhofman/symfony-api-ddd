<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Company\Application\Command\CreateCompanyCommand;
use App\Company\Domain\ValueObject\CompanyGroup;
use App\Company\Domain\ValueObject\CompanyName;
use App\Company\Domain\ValueObject\CompanyRanking;
use App\Company\Infrastructure\ApiPlatform\Payload\CompanyWriteModel;
use App\Shared\Application\Command\CommandBusInterface;
use Webmozart\Assert\Assert;

final readonly class CreateCompanyProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        Assert::isInstanceOf($data, CompanyWriteModel::class);

        /** @var CompanyWriteModel $data * */
        $command = new CreateCompanyCommand(
            new CompanyName($data->name),
            new CompanyGroup($data->group),
            new CompanyRanking($data->ranking),
        );

        $this->commandBus->dispatch($command);
    }
}
