<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webmozart\Assert\InvalidArgumentException;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('api_platform', [
        'title' => 'Symfony API DDD Template',
        'description' => 'Symfony API DDD Template - API Documentation',
        'version' => '0.1',
        'enable_docs' => true,
        'enable_swagger' => true,
        'enable_swagger_ui' => true,
        'defaults' => [
            'stateless' => true,
            'pagination_items_per_page' => 20,
            'cache_headers' => [
                'vary' => ['Content-Type', 'Authorization', 'Origin'],
            ],
            'extra_properties' => [
                'standard_put' => true,
            ],
        ],
        'show_webby' => false,
        'mapping' => [
            'paths' => [
                '%kernel.project_dir%/src/Company/Infrastructure/ApiPlatform/Resource/',
            ],
        ],
        'formats' => [
            'jsonld' => ['application/ld+json'],
            'json' => ['application/json'],
            'html' => ['text/html'],
        ],
        'patch_formats' => [
            'json' => ['application/merge-patch+json'],
        ],
        'swagger' => [
            'versions' => [3],
            'api_keys' => [
                'apiKey' => [
                    'name' => 'Authorization',
                    'type' => 'header',
                ],
            ],
        ],
        'collection' => [
            'pagination' => [
                'enabled' => true,
                'page_parameter_name' => 'page',
            ],
        ],
        'exception_to_status' => [
            InvalidArgumentException::class => 422,
        ],
    ]);
};
