<?php

declare(strict_types=1);

use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Infrastructure\ApiPlatform\State\Processor\CreateCompanyProcessor;
use App\Company\Infrastructure\ApiPlatform\State\Processor\DeleteCompanyProcessor;
use App\Company\Infrastructure\ApiPlatform\State\Processor\UpdateCompanyProcessor;
use App\Company\Infrastructure\ApiPlatform\State\Provider\CompanyCollectionProvider;
use App\Company\Infrastructure\ApiPlatform\State\Provider\CompanyItemProvider;
use App\Company\Infrastructure\Doctrine\DoctrineCompanyRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\Company\\', dirname(__DIR__, 2).'/src/Company');

    // Providers.
    $services->set(CompanyCollectionProvider::class)
        ->autoconfigure(false)
        ->tag('api_platform.state_provider', ['priority' => 0]);

    $services->set(CompanyItemProvider::class)
        ->autoconfigure(false)
        ->tag('api_platform.state_provider', ['priority' => 0]);

    // Processors.
    $services->set(CreateCompanyProcessor::class)
        ->autoconfigure(false)
        ->tag('api_platform.state_processor', ['priority' => 0]);

    $services->set(UpdateCompanyProcessor::class)
        ->autoconfigure(false)
        ->tag('api_platform.state_processor', ['priority' => 0]);

    $services->set(DeletecompanyProcessor::class)
        ->autoconfigure(false)
        ->tag('api_platform.state_processor', ['priority' => 0]);

    // Repositories.
    $services->set(CompanyRepositoryInterface::class)
        ->class(DoctrineCompanyRepository::class);
};
