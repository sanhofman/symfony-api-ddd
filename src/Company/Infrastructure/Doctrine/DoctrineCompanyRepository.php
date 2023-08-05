<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Doctrine;

use App\Company\Domain\Model\Company;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\ValueObject\CompanyGroup;
use App\Company\Domain\ValueObject\CompanyId;
use App\Shared\Infrastructure\Doctrine\DoctrineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

final class DoctrineCompanyRepository extends DoctrineRepository implements CompanyRepositoryInterface
{
    private const ENTITY_CLASS = Company::class;
    private const ALIAS = 'company';

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, self::ENTITY_CLASS, self::ALIAS);
    }

    public function save(Company $company): void
    {
        $this->em->persist($company);
        $this->em->flush();
    }

    public function remove(Company $company): void
    {
        $this->em->remove($company);
        $this->em->flush();
    }

    public function ofId(CompanyId $id): ?Company
    {
        return $this->em->find(self::ENTITY_CLASS, $id->value);
    }

    public function getMostRecent(): ?Company
    {
        return $this->query()
            ->orderBy('company.id.value', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function withCompanyGroup(CompanyGroup $companyGroup): static
    {
        return $this->filter(static function (QueryBuilder $qb) use ($companyGroup): void {
            $qb->where(sprintf('%s.group.value = :companyGroup', self::ALIAS))->setParameter('companyGroup', $companyGroup->value);
        });
    }
}
