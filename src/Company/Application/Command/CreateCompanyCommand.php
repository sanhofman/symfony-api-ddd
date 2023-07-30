<?php

declare(strict_types=1);

namespace App\Company\Application\Command;

use App\Company\Domain\ValueObject\CompanyGroup;
use App\Company\Domain\ValueObject\CompanyName;
use App\Company\Domain\ValueObject\CompanyRanking;
use App\Shared\Application\Command\CommandInterface;

final readonly class CreateCompanyCommand implements CommandInterface
{
    public function __construct(
        public CompanyName $name,
        public CompanyGroup $group,
        public CompanyRanking $ranking,
    ) {
    }
}
