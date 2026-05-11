<?php

namespace NbsPhp\Core\Transformers;

use Illuminate\Support\Facades\Request;
use League\Fractal\Pagination\CursorInterface;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Serializer\ArraySerializer as BaseArraySerializer;

class LazySerializer extends BaseArraySerializer
{
    public function collection(?string $resourceKey, array $data): array
    {
        return ['rows' => $data];
    }

    public function null(): ?array
    {
        return null;
    }

    public function meta(array $meta): array
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

    public function paginator(PaginatorInterface $paginator): array
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

    public function cursor(CursorInterface $cursor): array
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
