<?php


namespace Sanf\Core\Modules\ContactUs;


interface AskUsTopicRepositoryInterface
{
    public function list($limit, $offset);
}