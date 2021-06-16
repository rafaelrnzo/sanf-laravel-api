<?php


namespace Sanf\Core\Modules\Product;


interface ProductRepositoryInterface
{
    public function list($limit, $offset, $search);
}