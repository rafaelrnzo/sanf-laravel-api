<?php


namespace Sanf\Core\Modules\Plafond\Entities;


final class PlafondTypeEntity
{
    private array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function getId(): string
    {
        return $this->attributes['id'];
    }

    public function getName(): string
    {
        return $this->attributes['name'];
    }

    public function getTitle(): string
    {
        return $this->attributes['title'];
    }
}
