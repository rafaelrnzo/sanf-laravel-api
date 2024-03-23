<?php

namespace NbsPhp\Core\Transformers;

use Illuminate\Support\Facades\Request;
use League\Fractal\Pagination\CursorInterface;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Serializer\ArraySerializer as BaseArraySerializer;

class LazySerializer extends BaseArraySerializer
{
    /**
     * Serialize a collection to a plain array.
     *
     * @param string $resourceKey
     * @param array $data
     *
     * @return array
     */
    public function collection($resourceKey, array $data)
    {
        return ['rows' => $data];
    }

    /**
     * Serialize null resource.
     *
     * @return null
     */
    public function null()
    {
        return null;
    }

    public function meta(array $meta)
    {
        if (empty($meta)) {
            return [];
        }

        return $meta ?? [];
    }

    protected function getSortQueryParam()
    {
        return Request::input('sort_by') ?? 'earliest'; //TODO CONFIG DEFAULT SORT QUERY;
    }

    public function paginator(PaginatorInterface $paginator)
    {
        $pagination = [
            'total' => (int) $paginator->getTotal(),
            'count' => (int) $paginator->getCount(),
            'skip' => (int) $paginator->getSkip(),
            'limit' => (int) $paginator->getPerPage(),
            'sort_by' => $this->getSortQueryParam(),
        ];

        return ['metadata' => $pagination];
    }

    public function cursor(CursorInterface $cursor)
    {
        $cursor = [
            'current' => $cursor->getCurrent(),
            'prev' => $cursor->getPrev(),
            'next' => $cursor->getNext(),
            'count' => (int) $cursor->getCount(),
            'sort_by' => $this->getSortQueryParam(),
        ];

        return ['metadata' => $cursor];
    }
}
