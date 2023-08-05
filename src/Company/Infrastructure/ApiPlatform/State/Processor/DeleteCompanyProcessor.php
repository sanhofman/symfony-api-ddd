<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Company\Application\Command\DeleteCompanyCommand;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Infrastructure\ApiPlatform\Resource\CompanyResource;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Component\Uid\Ulid;
use Webmozart\Assert\Assert;

final readonly class DeleteCompanyProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        Assert::isInstanceOf($data, CompanyResource::class);

        $this->commandBus->dispatch(new DeleteCompanyCommand(new CompanyId(Ulid::fromString($data->id))));
    }
}
