<?php

namespace NbsPhp\ApiWrapper\Resource;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use stdClass;

/**
 * Base class for resource-driven (REST) APIs.
 * Query resources directly from the ApiResource class, rather than constructing HTTP requests manually.
 */
abstract class ApiResource
{
    //region Base
    /**
     * Key of id field.
     *
     * @var string
     */
    protected $idField = 'id';

    /**
     * ApiResource attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Modified ApiResource attributes.
     * @var array
     */
    protected $dirty = [];

    /**
     * ApiResource attribute cast types.
     *
     * @var string[]
     */
    protected $casts = [];

    /**
     * Built-in cast types for attribute casting.
     *
     * @var string[]
     */
    protected static $castTypes = [
        'array',
        'bool',
        'collection',
        'date',
        'datetime',
        'float',
        'int',
        'object',
        'string',
        'timestamp'
    ];

    /**
     * ApiResource constructor.
     * @param array $data
     * @param bool $exists
     */
    public function __construct(array $data = [], bool $exists = false)
    {
        $this->mergeAttributes($data);

        if (!$exists) {
            $this->dirty = [];
        }
    }

    /**
     * Dynamically retrieve attributes on the resource.
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the resource.
     *
     * @param string $key
     * @param $value
     */
    public function __set(string $key, $value): void
    {
        $this->setAttribute($key, $value);
    }
    //endregion

    //region Attributes
    //region Get/Set
    /**
     * Determine if the specified key is an attribute for the resource.
     *
     * @param string $key
     * @return bool
     */
    public function hasAttribute(string $key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Retrieve an attribute on the resource.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Set an attribute on the resource.
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setAttribute(string $key, $value): ApiResource
    {
        if ($this->castsAttribute($key)) {
            $value = $this->castAs($value, $this->getAttributeCastType($key));
        }

        $this->attributes[$key] = $value;
        $this->dirty[$key] = $value;

        return $this;
    }

    /**
     * Overwrite attribute values. If $clear is true, all attributes not included will be removed.
     *
     * @param array $attributes
     * @param bool $clear
     * @return $this
     */
    public function setAttributes(array $attributes, bool $clear = false): ApiResource
    {
        if ($clear) {
            $this->attributes = [];
        }

        return $this->mergeAttributes($attributes);
    }

    /**
     * Merge attribute values into the attributes array.
     * @param array $attributes
     * @return $this
     */
    public function mergeAttributes(array $attributes): ApiResource
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Get modified ApiResource attributes.
     * @return array
     */
    public function getDirty(): array
    {
        return $this->dirty;
    }
    //endregion

    //region Casts
    /**
     * Determine if the specified attribute should be cast.
     *
     * @param string $key
     * @return bool
     */
    public function castsAttribute(string $key): bool
    {
        return array_key_exists($key, $this->casts);
    }

    /**
     * Get the cast type for the specified attribute.
     *
     * @param string $key
     * @return string|null
     */
    public function getAttributeCastType(string $key): ?string
    {
        return $this->casts[$key] ?? null;
    }

    /**
     * Cast value as Carbon date object (no time component).
     *
     * @param string|int $value
     * @return Carbon
     */
    protected function castAsDate($value): Carbon
    {
        return $this->castAsDateTime($value)->startOfDay();
    }

    /**
     * Cast value as Carbon date object.
     *
     * @param string|int $value
     * @return Carbon
     */
    protected function castAsDateTime($value): Carbon
    {
        if (is_int($value)) {
            return Carbon::createFromTimestamp($value);
        } else {
            return Carbon::create($value);
        }
    }

    /**
     * Cast value as a Castable object instance.
     *
     * @param mixed $value
     * @param string $type
     * @return ApiResource|Collection|null
     */
    protected function castAsClass($value, string $type)
    {
        if (class_exists($type) && is_subclass_of($type, ApiResource::class)) {
            if (collect($value)->every(function ($value, $key) {
                return is_int($key) && is_array($value);
            })) {
                return $type::castMany($value);
            } else {

                return $type::cast($value);
            }
        }

        return null;
    }

    /**
     * Get ApiResource casted attribute value.
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected function castAs($value, string $type)
    {
        switch ($type) {
            case 'bool':
                return (bool) $value;
            case 'collection':
                return new Collection($value);
            case 'date':
                return $this->castAsDate($value);
            case 'datetime':
                return $this->castAsDateTime($value);
            case 'float':
                return (float) $value;
            case 'int':
                return (int) $value;
            case 'string':
                return (string) $value;
            default:
                return $this->castAsClass($value, $type);
        }
    }
    //endregion

    //region JSON
    /**
     * Convert ApiResource instance to JSON string.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Create a new ApiResource instance from JSON.
     *
     * @param string $data
     * @return static
     */
    public static function fromJson(string $data): ApiResource
    {
        return new static(json_decode($data, true));
    }
    //endregion

    //region ID
    /**
     * Get id field key.
     *
     * @return string
     */
    public function getIdField(): string
    {
        return $this->idField;
    }

    /**
     * Get id field value.
     *
     * @return string|int
     */
    public function getId()
    {
        return $this->getAttribute($this->getIdField());
    }
    //endregion
    //endregion

    //region Casts
    /**
     * Create new ApiResource instance from raw data.
     *
     * @param array|stdClass $data
     * @return ApiResource
     */
    public static function cast($data): ApiResource
    {
        return new static((array) $data);
    }

    /**
     * Create new ApiResource instances from raw data.
     *
     * @param array[]|stdClass[] $data
     * @return Collection
     */
    public static function castMany(array $data): Collection
    {
        return collect($data)->map(function ($datum) {
            return static::cast((array) $datum);
        });
    }

    /**
     * Return ApiResource raw data.
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = [];

        foreach ($this->attributes as $key => $value) {
            if (is_object($value) && method_exists($value, 'toArray')) {
                $value = $value->toArray();
            }

            $array[$key] = $value;
        }

        return $array;
    }
    //endregion

}
