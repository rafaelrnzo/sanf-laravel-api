<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Modules\Payment\UseCases\CheckPaymentByMidtransTransactionUseCase;
use Sanf\Integration\Modules\Midtrans\MidtransClient;

class CheckPaymentByMidtransTrxCommand extends Command
{
    protected $signature = 'payment:check-status-by-midtrans
        {midtrans_identifier : Midtrans order ID or transaction ID}';

    protected $description = 'Check payment status for a transaction order ID or transaction ID.';

    private CheckPaymentByMidtransTransactionUseCase $useCase;

    public function __construct(CheckPaymentByMidtransTransactionUseCase $useCase)
    {
        parent::__construct();

        $this->useCase = $useCase;
    }

    public function handle()
    {
        $midtransIdentifier = (string) $this->argument('midtrans_identifier');

        DB::transaction(
            fn () => $this->useCase->execute($midtransIdentifier)
        );
    }
}
