<?php

namespace NbsPhp\Core\Transformers;

use League\Fractal\Pagination\PaginatorInterface;

class LazyPaginatorAdapter implements PaginatorInterface
{
    protected $paginator;

    public function __construct($paginator) //TODO PAGINATOR INTERFACE
    {
        $this->paginator = $paginator;
    }

    public function getCurrentPage(): int
    {
        return max((int) ceil($this->paginator->skip / $this->paginator->limit), 1);
    }

    public function getLastPage(): int
    {
        return max((int) ceil($this->paginator->total / $this->paginator->limit), 1);
    }

    public function getTotal(): int
    {
        return (int) $this->paginator->total;
    }

    public function getCount(): int
    {
        return (int) $this->paginator->count;
    }

    public function getSkip()
    {
        return $this->paginator->skip;
    }

    public function getPerPage(): int
    {
        return (int) $this->paginator->limit;
    }

    public function getUrl(int $page): string
    {
        //TODO CORRECT IMPLEMENTATION
        return url()->current();
    }
}
