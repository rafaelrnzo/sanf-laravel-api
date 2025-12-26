<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Sanf\Integration\Modules\Midtrans\MidtransClient;

class CheckMidtransTransactionStatusCommand extends Command
{
    protected $signature = 'midtrans:transaction-status
        {identifier : Midtrans order ID or transaction ID}';

    protected $description = 'Check Midtrans transaction status for an order ID or transaction ID.';

    private const EXIT_SUCCESS = 0;

    private const EXIT_FAILURE = 1;

    private MidtransClient $midtransClient;

    public function __construct(MidtransClient $midtransClient)
    {
        parent::__construct();

        $this->midtransClient = $midtransClient;
    }

    public function handle(): int
    {
        $identifier = (string) $this->argument('identifier');

        $this->info(sprintf('Fetching Midtrans status for "%s"...', $identifier));

        $response = $this->midtransClient->getTransactionStatus($identifier);

        if ($response === null) {
            $this->warn('Transaction not found.');

            return self::EXIT_FAILURE;
        }

        $this->line('Raw response:');
        $this->line(json_encode($response->raw, JSON_PRETTY_PRINT));

        return self::EXIT_SUCCESS;
    }
}
