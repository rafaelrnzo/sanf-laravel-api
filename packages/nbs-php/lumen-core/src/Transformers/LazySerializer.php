<?php


namespace NbsPhp\Core\Transformers;


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

        return ['metadata' => $meta['pagination'] ?? []];
    }

    public function paginator(PaginatorInterface $paginator)
    {
        $pagination = [
            'count' => (int)$paginator->getTotal(),
            'limit' => (int)$paginator->getPerPage(),
            'sort_by' => (string)$paginator->getSort(),
        ];

        return ['pagination' => $pagination];
    }
}
