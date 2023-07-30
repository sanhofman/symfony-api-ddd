<?php

declare(strict_types=1);

namespace App\Company\Application\Command;

use App\Company\Domain\ValueObject\CompanyId;
use App\Shared\Application\Command\CommandInterface;

final readonly class DeleteCompanyCommand implements CommandInterface
{
    public function __construct(
        public CompanyId $id,
    ) {
    }
}
