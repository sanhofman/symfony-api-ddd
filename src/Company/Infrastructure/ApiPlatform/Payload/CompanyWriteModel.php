<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\ApiPlatform\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class CompanyWriteModel
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public string $name = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public string $group = '';

    #[Assert\PositiveOrZero]
    public int $ranking = 1;
}
