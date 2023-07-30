<?php

declare(strict_types=1);

namespace App\Company\Application\Command;

use App\Company\Domain\ValueObject\CompanyGroup;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\CompanyName;
use App\Company\Domain\ValueObject\CompanyRanking;
use App\Shared\Application\Command\CommandInterface;

final readonly class UpdateCompanyCommand implements CommandInterface
{
    public function __construct(
        public CompanyId $id,
        public CompanyName $name,
        public CompanyGroup $group,
        public CompanyRanking $ranking,
    ) {
    }
}
