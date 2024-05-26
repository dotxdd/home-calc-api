<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CostLimitExceededMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cost;
    public $limit;
    public $exceededAmount;
    public $period;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($cost, $limit, $exceededAmount, $period)
    {
        $this->cost = $cost;
        $this->limit = $limit;
        $this->exceededAmount = $exceededAmount;
        $this->period = $period;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Cost Limit Exceeded')
            ->view('emails.cost_limit_exceeded');
    }
}
