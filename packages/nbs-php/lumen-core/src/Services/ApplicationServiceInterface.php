<?php


namespace NbsPhp\Core\Services;


interface ApplicationServiceInterface
{
    /**
     * @param $input
     * @return mixed
     */
    public function execute($input);
}
