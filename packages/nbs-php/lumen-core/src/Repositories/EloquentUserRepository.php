<?php

namespace NbsPhp\Core\Repositories;


use Illuminate\Support\Facades\DB;
use Sanf\Core\Modules\User\AuthModel;

class EloquentUserRepository extends AbstractEloquentRepository implements UserRepositoryInterface
{
    protected $model;
    protected $userMetadataModel;

    public function __construct(AuthModel $model)
    {
        $this->model = $model;
    }

    public function findById($id)
    {
        return $this->model->newQuery()->find($id);
    }

    public function getMetadata($userId, $keys = null)
    {
        $query = $this->userMetadataModel->newQuery()->where('user_id', $userId);
        if (is_array($keys)) {
            $query->whereIn('key', $keys);
            return json_decode(json_encode($query->get()));
        } elseif ($keys != null) {
            $query->where('key', $keys);
            return json_decode(json_encode($query->first()));
        }
        return json_decode(json_encode($query->get()));
    }

    public function createMetadata($userId, $key, $value)
    {
        $metadata = $this->userMetadataModel->newQuery()
            ->forceCreate([
                'user_id' => $userId,
                'key' => $key,
                'value' => $value
            ]);

        return $metadata;
    }

    public function updateMetadata($userId, $key, $value, $version = null)
    {
        $query = $this->userMetadataModel->newQuery()
            ->where('user_id', $userId)
            ->where('key', $key);

        if ($version == null) {
            return $query->where('version', $version)->update([
                'value' => $value,
                'version' => DB::raw('version+1')
            ]);
        }

        return $query->where('version', $version)->update([
            'value' => $value,
            'version' => $version + 1
        ]);
    }
}
