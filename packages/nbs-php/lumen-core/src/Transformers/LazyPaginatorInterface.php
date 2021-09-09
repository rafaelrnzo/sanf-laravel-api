<?php


namespace NbsPhp\Core\Transformers;


use League\Fractal\Pagination\PaginatorInterface;

interface LazyPaginatorInterface extends PaginatorInterface
{
    /**
     * Get sort by strategy.
     *
     * @return int
     */
    public function getSort();
}
