<?php


namespace NbsPhp\Core\Services;


interface ApplicationServiceInterface
{
    /**
     * @param null $dto
     * @return mixed
     */
    public function execute($dto = null);
}
