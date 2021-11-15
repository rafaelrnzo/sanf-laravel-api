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

    public function getCurrentPage()
    {
        return max((int) ceil($this->paginator->skip / $this->paginator->limit), 1);
    }

    public function getLastPage()
    {
        return max((int) ceil($this->paginator->total / $this->paginator->limit), 1);
    }

    public function getTotal()
    {
        return $this->paginator->total;
    }

    public function getCount()
    {
        return $this->paginator->count;
    }

    public function getSkip()
    {
        return $this->paginator->skip;
    }

    public function getPerPage()
    {
        return $this->paginator->limit;
    }

    public function getUrl($page)
    {
        //TODO CORRECT IMPLEMENTATION
        return url()->current();
    }
}
