<?php

declare(strict_types=1);

namespace App\Company\Domain\Model;

use App\Company\Domain\ValueObject\CompanyGroup;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\CompanyName;
use App\Company\Domain\ValueObject\CompanyRanking;
use App\Shared\Domain\Model\TimestampableInterface;
use App\Shared\Domain\ValueObject\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Company implements TimestampableInterface
{
    use TimestampableTrait;

    #[ORM\Embedded(columnPrefix: false)]
    private readonly CompanyId $id;

    public function __construct(
        #[ORM\Embedded(columnPrefix: false)]
        private CompanyName $name,

        #[ORM\Embedded(columnPrefix: false)]
        private CompanyGroup $group,

        #[ORM\Embedded(columnPrefix: false)]
        private CompanyRanking $ranking,
    ) {
        $this->id = new CompanyId();
    }

    public function update(
        ?CompanyName $name = null,
        ?CompanyGroup $group = null,
        ?CompanyRanking $ranking = null,
    ): void {
        $this->name = $name ?? $this->name;
        $this->group = $group ?? $this->group;
        $this->ranking = $ranking ?? $this->ranking;
    }

    public function id(): CompanyId
    {
        return $this->id;
    }

    public function name(): CompanyName
    {
        return $this->name;
    }

    public function group(): CompanyGroup
    {
        return $this->group;
    }

    public function ranking(): CompanyRanking
    {
        return $this->ranking;
    }
}
