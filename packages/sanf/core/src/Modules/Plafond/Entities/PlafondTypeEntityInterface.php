<?php


namespace Sanf\Core\Modules\Plafond\Entities;


interface PlafondTypeEntityInterface
{
    public function __construct(array $attributes);

    public function getId(): string;

    public function getName(): string;

    public function getTitle(): string;
}
