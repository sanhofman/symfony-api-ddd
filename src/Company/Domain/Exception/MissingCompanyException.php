<?php

declare(strict_types=1);

namespace App\Company\Domain\Exception;

use App\Company\Domain\ValueObject\CompanyId;

final class MissingCompanyException extends \RuntimeException
{
    public function __construct(CompanyId $id, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('Cannot find company with id %s', (string) $id), $code, $previous);
    }
}
