<?php


namespace NbsPhp\Core\Services;


use NbsPhp\Core\Database\TransactionalSessionInterface;

class TransactionalApplicationService implements ApplicationServiceInterface
{
    private $session;
    private $service;

    public function __construct(
        ApplicationServiceInterface $service,
        TransactionalSessionInterface $session
    ) {
        $this->session = $session;
        $this->service = $service;
    }

    public function execute($dto = null)
    {
        $operation = function() use($dto) {
            return $this->service->execute($dto);
        };

        return $this->session->executeAtomically(
            $operation->bindTo($this)
        );
    }
}
