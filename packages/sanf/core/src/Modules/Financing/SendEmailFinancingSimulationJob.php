<?php


namespace Sanf\Core\Modules\Financing;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use NbsPhp\Core\Mail\BaseMail;


class SendEmailFinancingSimulationJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $financing;

    protected $emailRecipients;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($financing, $emailRecipients)
    {
        $this->financing = $financing;
        $this->emailRecipients = $emailRecipients;
    }

    public function handle()
    {
        $financingCalculationMail = (new BaseMail())
            ->subject('')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'));

        return Mail::to($this->emailRecipients)->send($financingCalculationMail);
    }
}
