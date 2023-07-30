<?php

declare(strict_types=1);

namespace App\Tests\Company\DummyFactory;

use App\Company\Domain\Model\Company;
use App\Company\Domain\ValueObject\CompanyGroup;
use App\Company\Domain\ValueObject\CompanyName;
use App\Company\Domain\ValueObject\CompanyRanking;

final class DummyCompanyFactory
{
    private function __construct()
    {
    }

    public static function createCompany(
        string $name = 'name',
        string $group = 'group',
        int $ranking = 1,
    ): Company {
        return new Company(
            new CompanyName($name),
            new CompanyGroup($group),
            new CompanyRanking($ranking),
        );
    }
}
