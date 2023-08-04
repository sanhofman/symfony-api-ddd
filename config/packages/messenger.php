<?php

declare(strict_types=1);

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Application\Query\QueryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'messenger' => [
            'default_bus' => 'command.bus',
            'failure_transport' => 'failed',
            'buses' => [
                'command.bus' => [],
                'query.bus' => [],
            ],
            'transports' => [
                'failed' => [
                    'dsn' => '%env(MESSENGER_DOCTRINE_FAILED_TRANSPORT_DSN)%',
                    'options' => [
                        'table_name' => 'messenger_failed',
                    ],
                ],
                'sync' => 'sync://',
            ],
            'routing' => [
                QueryInterface::class => 'sync',
                CommandInterface::class => 'sync', // @TODO:: use async writes with outbox.
            ],
        ],
    ]);
};
