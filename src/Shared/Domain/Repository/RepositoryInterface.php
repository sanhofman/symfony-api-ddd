<?php

declare(strict_types=1);

namespace App\Shared\Domain\Repository;

use Countable;

interface RepositoryInterface extends \IteratorAggregate, Countable
{
    public function getIterator(): \Iterator;

    public function count(): int;

    public function paginator(): ?PaginatorInterface;

    public function withPagination(int $page, int $itemsPerPage): static;

    public function withoutPagination(): static;
}
