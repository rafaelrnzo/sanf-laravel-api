<?php


namespace NbsPhp\Core\Services;


interface ApplicationServiceInterface
{
    /**
     * @param $dto
     * @return mixed
     */
    public function execute($dto);
}
