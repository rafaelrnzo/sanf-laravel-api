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

    public function getCurrentPage()
    {
        return 0;
    }

    public function getLastPage()
    {
        return $this->data->count();
    }

    public function getTotal()
    {
        return $this->data->count();
    }

    public function getCount()
    {
        return $this->data->count();
    }

    public function getPerPage()
    {
        return $this->data->count();
    }

    public function getUrl($page)
    {
        return url()->current();
    }
}
