<?php


namespace NbsPhp\Core\Services;


interface PipelineServiceInterface
{
    public function handle($dto, \Closure $next);
}
