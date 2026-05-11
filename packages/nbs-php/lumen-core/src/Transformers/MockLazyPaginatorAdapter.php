<?php

namespace NbsPhp\Core\Transformers;

use League\Fractal\Pagination\PaginatorInterface;

class MockLazyPaginatorAdapter implements PaginatorInterface
{
    protected $data;

    public function __construct($data)
    {
        $this->data = collect($data);
    }

    public function getCurrentPage(): int
    {
        return 0;
    }

    public function getLastPage(): int
    {
        return (int) $this->data->count();
    }

    public function getTotal(): int
    {
        return (int) $this->data->count();
    }

    public function getCount(): int
    {
        return (int) $this->data->count();
    }

    public function getPerPage(): int
    {
        return (int) $this->data->count();
    }

    public function getUrl(int $page): string
    {
        return (string) url()->current();
    }
}
