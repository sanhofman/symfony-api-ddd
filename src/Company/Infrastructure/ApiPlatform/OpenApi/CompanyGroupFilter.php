<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\ApiPlatform\OpenApi;

use ApiPlatform\Api\FilterInterface;
use Symfony\Component\PropertyInfo\Type;

final readonly class CompanyGroupFilter implements FilterInterface
{
    public function getDescription(string $resourceClass): array
    {
        return [
            'companyGroup' => [
                'property' => 'companyGroup',
                'type' => Type::BUILTIN_TYPE_STRING,
                'required' => false,
            ],
        ];
    }
}
