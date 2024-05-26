<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CostLimitExceeded extends Notification
{
    use Queueable;

    protected $cost;
    protected $limit;
    protected $period;

    public function __construct($cost, $limit, $period)
    {
        $this->cost = $cost;
        $this->limit = $limit;
        $this->period = $period;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Cost Limit Exceeded')
            ->line('You have exceeded your ' . $this->period . ' limit for the cost type: ' . $this->cost->costType->name)
            ->line('Cost Date: ' . $this->cost->date)
            ->line('Exceeded by: ' . ($this->cost->price - $this->limit))
            ->line('Thank you for using our application!');
    }
}
