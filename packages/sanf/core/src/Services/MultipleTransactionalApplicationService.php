<?php

namespace Sanf\Core\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Database\MultipleTransactionalSessionInterface;

class MultipleTransactionalApplicationService implements ApplicationServiceInterface
{
    private $session;
    private $service;

    public function __construct(
        ApplicationServiceInterface $service,
        MultipleTransactionalSessionInterface $session
    ) {
        $this->session = $session;
        $this->service = $service;
    }

    public function execute($dto = null)
    {
        $operation = function () use ($dto) {
            return $this->service->execute($dto);
        };

        return $this->session->executeAtomically(
            $operation->bindTo($this)
        );
    }
}
