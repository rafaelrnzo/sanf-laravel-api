<?php

namespace NbsPhp\Core\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                $id
 * @property string             $name
 * @property Carbon|string|null $updated_at
 */
class EntityTypeModel extends Model
{
    protected $table = 'user_entity_type'; //overridden in constructor from config auth

    public function __construct(array $attributes = [])
    {
        $this->table = config('auth.table_names.entity_type');
        parent::__construct($attributes);
    }
}
