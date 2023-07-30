<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\ApiPlatform\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class ResetRankingCompaniesWriteModel
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $ranking = 1;
}
