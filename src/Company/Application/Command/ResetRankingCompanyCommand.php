<?php

declare(strict_types=1);

namespace App\Company\Application\Command;

use App\Company\Domain\ValueObject\CompanyRanking;
use App\Shared\Application\Command\CommandInterface;

final readonly class ResetRankingCompanyCommand implements CommandInterface
{
    public function __construct(
        public CompanyRanking $companyRanking,
    ) {
    }
}
