<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\BrowseDistrictDto;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\Modules\TekenAja\TekenAjaApiClient;

final class BrowseDistrictService implements ApplicationServiceInterface
{
    protected AuthModel $userRepository;
    protected TekenAjaApiClient $client;

    public function __construct(
        TekenAjaApiClient $client,
        AuthModel $userRepository
    ) {
        $this->userRepository = $userRepository;
        $this->client = $client;
    }

    /**
     * @param null $dto
     * @return object
     * @throws UserNotFoundException
     */
    public function execute($dto = null): object
    {
        /** @var BrowseDistrictDto $dto */
        $user = $this->userRepository->newQuery()->find($dto->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $result = $this->client->getDistricts($dto->province_id);
        $mapping = array_map(function ($key, $item) use ($dto) {
            return (object) [
                'xid' => $key,
                'name' => mb_convert_case($item, MB_CASE_TITLE, 'UTF-8'),
                'province_id' => $dto->province_id,
                'created_at' => Carbon::now(),
            ];
        }, array_keys($result['data']), $result['data']);

        $data = collect($mapping)
            ->when($dto->keyword, function ($collection, $value) {
                return $collection->filter(function ($item) use ($value) {
                    return stristr($item->name, $value);
                });
            })
            ->when(!$dto->sort_by, function ($collection) {
                return $collection->sortBy(function ($item) {
                    return $item->name;
                });
            })
            ->when($dto->sort_by, function ($collection, $value) {
                if ($value == 'asc') {
                    return $collection->sortBy(function ($item) {
                        return $item->name;
                    });
                }

                return $collection->sortByDesc(function ($item) {
                    return $item->name;
                });
            });

        return (object) [
            'data' => $data,
            'paginate' => (object) [
                'total' => $data->count(),
                'count' => $data->count() ?? 0,
                'skip' => $dto->skip ?? 0,
                'limit' => $dto->limit ?? null,
                'sort_by' => $dto->sortBy ?? '',
            ],
        ];
    }
}
